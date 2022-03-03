<?php
$ID='pmstreepanelactivity';
if(!empty($activityName['Activity']['project'])) $ID='pmstreepanel';
?>
<?php echo $html->css(array('project_task')); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
            overflow: auto
        }
    </style>
<![endif]-->
<div id="loading-mask"></div>
<div id="loading">
  <div class="loading-indicator">
  </div>
</div>

<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    if(empty($activityName['Activity']['project'])){
        echo $this->Form->create('Export', array(
            'id' => 'export-item-list',
            'type' => 'POST',
            'url' => array('controller' => 'activity_tasks', 'action' => 'export')));
        echo $this->Form->input('list', array('type' => 'text', 'value' =>$activity_id));
        echo $this->Form->end();
    }else{
        echo $this->Form->create('Export', array(
        'type' => 'POST',
        'id' => 'export-item-list',
        'url' => array('controller' => 'project_tasks', 'action' => 'exportExcel',$project_id)));
        echo $this->Form->input('list', array('type' => 'text', 'value' => 'Activity', ));
        echo $this->Form->end();
    }
    ?>
</fieldset>
<style type="text/css">
#displayFreeze {display: inline-block; vertical-align: top;}
#wd-container-main .wd-layout{padding-bottom: 15px !important;}
#menuitem-1060-itemEl, #menuitem-1059-itemEl, #menuitem-1053-itemEl, #menuitem-1052-itemEl{display: none;}

#table-in-ex tr th, tr td.left-column{
        background: url('<?php echo $this->Html->url('/img/front/bg-head-table.png');?>') repeat-x #06427A;
        border-right: 1px solid #5fa1c4;
        color: #fff;
        height: 28px;
        vertical-align: middle;
        padding-left: 3px;
    }
#table-in-ex tr th{
	text-align:center;
}
#table-in-ex {width: 640px;float: left;}
#table-in-ex tr td.left-column{text-align: left;}
#table-in-ex tr td{
    border: 1px solid silver;
    height: 28px;
    text-align: right;
}
#gantt-display{margin-bottom:10px;}
#displayWorkload tr td{padding-top: 5px;padding-right: 3px;}
.invalid{
	border:1px solid #F00 !important;
}

.context-menu-filter{
    width:100% !important;
    background:none !important;
    border:none !important;
}
.txtsearch{
    height:22px;
    width:100%;
}
.treeExpand{
    top:0 !important;
    right:0 !important;
    bottom: 0 !important;
    left: 0 !important;
    position:fixed !important;
    height: 100% !important;
    z-index:100 !important;
}
.treeFullSize{
    width: 100%;
    height: 100%;
}
.w {
    font-size: 12px;
    padding: 5px 10px;
}
.w input[type="text"] {
    font-size: 12px;
    padding: 4px;
    border: 1px solid #bbb;
    background: #fff;
}
.w select {
    padding: 3px;
    font-size: 12px;
    border: 1px solid #bbb;
    background: #fff;
}
.w input:disabled {
    background: #f0f0f0;
    border-color: #ddd;
}

#assign-list {
}
#assign-table {
    border-collapse: collapse;
    width: 100%;
}
#assign-table td {
    border: 1px solid #bbb;
    padding: 5px;
}
#assign-table thead td,
#assign-table tfoot td {
    background: rgb(22, 54, 92);
    color: #fff;
    font-weight: bold;
    text-align: right;
    border-color: rgb(22, 54, 92);
}
#assign-table thead td {
    vertical-align: middle;
}
#assign-table tbody tr td:not(:first-child) {
    text-align: right;
}
#assign-table tbody td input:focus {
    color: #08c;
}

#assign-table td.nct-date,
#assign-table td.bold {
    border-color: rgb(22, 54, 92);
}
#assign-table td.nct-date {
    position: relative;
}
#assign-table td.nct-date .cancel {
    background: url(/img/reject-small.png) center center no-repeat;
    width: 20px;
    height: 20px;
    padding: 0 !important;
    border-radius: 4px;
    display: inline-block;
    position: absolute;
    right: -710px;
    top: 2px;
    text-decoration: none;
}

.null-cell {
    background: transparent !important;
}
.hidden-cell {
    border-right: 0 !important;
}
.hidden-cell + td {
    border-left: 0 !important;
}
.ciu-cell {
    text-align: right;
}
.gradient {
    background: #ffffff;
    background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJod…EiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
    background: -moz-linear-gradient(top, #ffffff 0%, #f0f0f0 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f0f0f0));
    background: -webkit-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
    background: -o-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
    background: -ms-linear-gradient(top, #ffffff 0%,#f0f0f0 100%);
    background: linear-gradient(to bottom, #ffffff 0%,#f0f0f0 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f0f0f0',GradientType=0 );
    padding: 7px 15px;
    border: 1px solid #d0d0d0;
    cursor: pointer;
    border-radius: 4px;
}
.gradient:hover,
.gradient:focus {
    border-color: #bbb;
}

a.disabled {
    -webkit-filter: grayscale(100%);
    filter: grayscale(100%);
    filter: gray;
    cursor: default;
}
#special-task-info .wd-input{
    float: left;
    overflow: hidden;
}
#special-task-info .wd-input label{
    width: 75px;
    font-weight: bold;
    display: inline-block;
    text-align: right;
    /*padding-left: 5px;*/
}
#special-task-info .wd-input input[type="text"], #special-task-info .wd-input select{
    border: 1px solid #bbb;
    padding: 6px;
    width: 250px;
    margin-left: 5px;
    margin-bottom: 5px;
}
#special-task-info .wd-input select{
    width: auto;
    }
hr {
    display: block;
    clear: both;
    height: 1px;
    border: 0;
    border-top: 3px solid rgb(22, 54, 92);
    margin: 10px -10px !important;
}
.w .multiSelect, .w .multiSelectOptions {
    width: 585px !important;
}
.w .multiSelect span {
    width: 560px !important;
}
.w .multiSelect {
    border-color: #bbb !important;
    padding: 1px !important;
    height: auto !important;
    margin-bottom: 5px !important;
}
.w .multiSelectOptions label {
    clear: both;
}
.w .multiSelectOptions label input {
    border: none !important;
    background: transparent  !important;
}

.ui-datepicker {
    border: 1px solid #bbb;
}

.ui-datepicker .ui-datepicker-header {
    background: #f0f0f0;
}
.ui-datepicker .ui-datepicker-title {
    background: #f0f0f0;
    color: #08c;
    text-shadow: 0 0 0 ;
    border-bottom: 1px solid #ddd;
}

table.ui-datepicker-calendar {border-collapse: separate;}
.ui-datepicker-calendar td {border: 1px solid transparent;}

.ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
    background: #fff none;
    border-color: #3892d3;
}
.ui-datepicker table td.ui-datepicker-today {
    background: #fff;
}
.ui-datepicker table td.ui-datepicker-today a,
.ui-datepicker table td.ui-datepicker-today span {
    border: 1px solid #8b0000;
    background: #fff;
    color: #000;
}
.ui-datepicker table td.ui-datepicker-today.ui-state-disabled a,
.ui-datepicker table td.ui-datepicker-today.ui-state-disabled span {
    color: #cdcdcd;
}
.ui-datepicker-calendar td,
.ui-datepicker-calendar th {
    padding: 0;
    background-color: #fff;
}
.ui-datepicker-calendar td a,
.ui-datepicker-calendar td a:hover,
.ui-datepicker-calendar td span {
    padding: 3px;
    display: block;
    border: 1px solid transparent;
}
.btn1 {
    background-attachment: scroll;
    background-clip: border-box;
    background-color: transparent;
    background-image: url("/img/front/bg-add-project.png");
    background-origin: padding-box;
    background-position: left top;
    background-repeat: no-repeat;
    background-size: auto auto;
    color: #FFFFFF;
    display: inline-block;
    height: 33px;
    line-height: 33px;
    padding-left: 27px;
    text-decoration: none;
    border: 0;
    cursor: pointer !important;
}
.btn1 span {
    background-attachment: scroll;
    background-clip: border-box;
    background-color: transparent;
    background-image: url("/img/front/bg-add-project-right.png");
    background-origin: padding-box;
    background-position: right top;
    background-repeat: no-repeat;
    background-size: auto auto;
    display: block;
    height: 33px;
    padding-bottom: 0;
    padding-left: 2px;
    padding-right: 15px;
    padding-top: 0;
    font-size: 13px;
    text-decoration: none;
}
.btn1:hover {
    background-position: bottom left;
}
.btn1:hover span {
    background-position: bottom right;
}

.btn2 {
    background-attachment: scroll;
    background-clip: border-box;
    background-color: transparent;
    background-image: url("/img/front/bg-reload.png");
    background-origin: padding-box;
    background-position: left top;
    background-repeat: no-repeat;
    background-size: auto auto;
    display: inline-block;
    height: 33px;
    width: 81px;
    color: #000 !important;
    line-height: 33px;
    padding-left: 30px;
    text-decoration: none;
    border: 0;
    cursor: pointer !important;
}
.btn2:hover {
    background-position: bottom left;
}


#nct-range-type:disabled {
    background: #eee;
}

.hide-on-mobile {
    <?php if( $isMobile ): ?>display: none !important;<?php endif ?>
}
.ui-dialog-titlebar-close.ui-corner-all {
    visibility: visible !important;
}
.multiSelect, .multiSelectOptions {
    width: 585px !important;
}
.task_blue{
    background-image: url("/img/extjs/icon-square.png");
    background-repeat: no-repeat;
    display: block;
    padding-left: 20px;
    cursor: pointer;
}
.task_red{
    background-image: url("/img/extjs/icon-triangle.png");
    background-repeat: no-repeat;
    display: block;
    padding-left: 20px;
    cursor: pointer;
}
.task_none{
    padding-left: 20px;
}
.filter-by-type {
    display: inline-block;
}
.filter-type {
    display: inline-block;
    width: 32px;
    height: 32px;
    cursor: pointer;
    border: 1px solid transparent;
}
.type-green {
    background: url(/img/extjs/icon-square-32.png) center no-repeat;
}
.type-red {
    background: url(/img/extjs/icon-triangle-32.png) center no-repeat;
    padding: 0 2px;
}
.type-green.type-focus {
    border-color: #018732;
}
.type-red.type-focus {
    border-color: #ff2600;
}
</style>
<!-- /export excel  -->
<!-- dialog_import -->
<div id="dialog_import_CSV" title="<?php __('Import CSV file') ?>" class="buttons" style="display:none">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file', 'target' => '_blank',
        'url' => array('controller' => 'activity_tasks', 'action' => 'import_csv', $activityName['Activity']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(Allowed file type: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title" style="margin-bottom:0 !important;">
                    <h2 class="wd-t1" style="line-height: 1.25"><?php echo sprintf(__("Activity Tasks for %s", true), $activityName['Activity']['name']); ?></h2>
                    <a href="javascript:;" id="clean-filters" title="<?php __('Reset filter') ?>" class="btn btn-reset"></a>
                    <a href="javascript:;" onclick="expandTaskScreen();" class="btn btn-fullscreen hide-on-mobile"></a>
                    <a href="<?php echo $this->Html->url(array('action' => 'visions', $activityName['Activity']['id']));?>" class="btn-text">
                        <i class="icon-eye"></i>
                    </a>
                    <a href="javascript:void(0);" class="export-excel-icon-all hide-on-mobile" id="export-submit"><span><?php __('Export Excel') ?></span></a>
                    <a href="javascript:void(0)" class="import-excel-icon-all hide-on-mobile" id="import_CSV" title="<?php __('Import CSV')?>"><span><?php __('Import CSV') ?></span></a>
                    <div class="filter-by-type">
                        <span class="filter-type type-green" data-type="green"></span>
                        <span class="filter-type type-red" data-type="red"></span>
                    </div>
                    <?php if($settingP['ProjectSetting']['show_freeze']):?>
                    <div class="buttons hide-on-mobile" style="display: inline-block; vertical-align: top;">
                        <?php if((($myRole=='pm')||($myRole=='admin'))&&($activityName['Activity']['is_freeze']==0)){?>
                            <a href="<?php echo $this->Html->url('/activity_tasks/freeze/'.$activity_id);?>" id="submit-freeze-all-top" class="validate-for-validate validate-for-validate-top" title="<?php __('Freeze')?>"><span><?php __('Freeze'); ?></span></a>
                        <?php }?>
                        <?php if(($myRole=='admin')&&($activityName['Activity']['is_freeze']==1)){?>
                            <a href="<?php echo $this->Html->url('/activity_tasks/unfreeze/'.$activity_id);?>" id="submit-unfreeze-all-top" class="validate-for-reject validate-for-reject-top" title="<?php __('Unfreeze')?>"><span><?php __('Unfreeze'); ?></span></a>
                         <?php }?>
                       <ul id="displayFreeze">
                         <?php if(($activityName['Activity']['freeze_by']!=null)&&($activityName['Activity']['is_freeze']==1)){?>
                            <li class="unfreeze-freeze"><?php echo sprintf(__('Freezed by %s at %s', true), $modifyF['Employee']['fullname'], date('d/m/Y h:i:s',$activityName['Activity']['freeze_time']));?>  </li>
                        <?php }elseif(($activityName['Activity']['freeze_by']!=null)&&($activityName['Activity']['is_freeze']==0)){?>
                            <li class="unfreeze-freeze"><?php echo sprintf(__('Unfreezed by %s at %s', true), $modifyF['Employee']['fullname'], date('d/m/Y h:i:s',$activityName['Activity']['freeze_time']));?>  </li>
                        <?php }?>
                            <li>
                                <?php
                                echo $this->Form->input('Activity.off_freeze', array(
                                    'rel' => 'no-history',
                                    'label' => '',
                                    'checked'=>$activityName['Activity']['off_freeze']?true:false,
                                    'div' => false,
                                    'type' => 'checkbox', 'legend' => false, 'fieldset' => false
                                ));?>
                                <label for="ActivityOffFreeze"><?php echo __('Display initial information',true);?></label>
                            </li>
                        </ul>
                    </div>
                    <?php endif;?>
                 <div id="gantt-display">
                     <table id="table-in-ex" <?php echo isset($companyConfigs['display_synthesis']) && $companyConfigs['display_synthesis'] == '0' ?'style="display:none"':''?>>
                        <thead>
                            <tr  class="tbHead">
                                <th> &nbsp; </th>
                                <th><?php echo __('Budget');?></th>
                                <th><?php echo __('Workload');?></th>
                                <th><?php echo __('VAR%');?></th>
                                <th><?php echo __('Consumed');?></th>
                                <th><?php echo __('Remain');?></th>
                            </tr>
                        </thead>
                        <tbody id="displayWorkload">
                            <tr class="internal-line">
                                <td class="left-column"><?php echo __('Internal');?></td>
                                <td class="display-budget">
                                        <span class="display-value">
                                            <?php if($budgetInters['ProjectBudgetInternalDetail']['total']!=0){
                                                    echo $this->Number->format(round($budgetInters['ProjectBudgetInternalDetail']['total'],2), array(
                                                            'places' => 2,
                                                            'before' => ' ',
                                                            'escape' => false,
                                                            'decimals' => ',',
                                                            'thousands' => ' '
                                                        ));
                                                   }else{
                                                    echo '0,00';
                                                   }
                                            ?>
                                        </span>
                                        <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-workload int-wl">
                                    <span class="display-value">
                                        <?php /*echo $this->Number->format(round($workloadInter,2), array(
                                                        'places' => 2,
                                                        'before' => '',
                                                        'escape' => false,
                                                        'decimals' => ',',
                                                        'thousands' => ' '
                                                    ));*/
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-var">
                                        <span class="display-value">
                                            <?php /*if($budgetInters['ProjectBudgetInternalDetail']['total']!=0){
                                                     echo $this->Number->format(round(($workloadInter/$budgetInters['ProjectBudgetInternalDetail']['total']-1)*100,2), array(
                                                            'places' => 2,
                                                            'before' => ' ',
                                                            'escape' => false,
                                                            'decimals' => ',',
                                                            'thousands' => ' '
                                                        )).' %';
                                                }else{
                                                    echo '0,00%';
                                            } */ ?>
                                        </span>
                                </td>
                                <td class="display-internal-consumed">
                                    <span class="display-value">
                                        <?php echo $this->Number->format(round($consumedInter,2), array(
                                                        'places' => 2,
                                                        'before' => ' ',
                                                        'escape' => false,
                                                        'decimals' => ',',
                                                        'thousands' => ' '
                                                    ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-internal-remain">
                                    <span class="display-value">
                                        <?php echo $this->Number->format(round($remainInter, 2), array(
                                                        'places' => 2,
                                                        'before' => ' ',
                                                        'escape' => false,
                                                        'decimals' => ',',
                                                        'thousands' => ' '
                                                    ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                            </tr>
                            <tr class="external-line">
                                <td class="left-column"><?php echo __('External');?></td>
                                <td class="display-budget">
                                    <span class="display-value">
                                        <?php if($budgetExters['ProjectBudgetExternal']['total']!=0){
                                                echo $this->Number->format(round($budgetExters['ProjectBudgetExternal']['total'],2), array(
                                                        'places' => 2,
                                                        'before' => ' ',
                                                        'escape' => false,
                                                        'decimals' => ',',
                                                        'thousands' => ' '
                                                    ));
                                               }else{
                                                echo '0,00';
                                               }
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-workload ext-wl">
                                    <span class="display-value">
                                        <?php
                                            echo $this->Number->format(round($workloadExter,2), array(
                                                    'places' => 2,
                                                    'before' => ' ',
                                                    'escape' => false,
                                                    'decimals' => ',',
                                                    'thousands' => ' '
                                                ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-var">
                                    <span class="display-value">
                                        <?php if($budgetExters['ProjectBudgetExternal']['total']!=0){
                                                 echo $this->Number->format(round(($workloadExter/$budgetExters['ProjectBudgetExternal']['total']-1)*100,2), array(
                                                        'places' => 2,
                                                        'before' => ' ',
                                                        'escape' => false,
                                                        'decimals' => ',',
                                                        'thousands' => ' '
                                                    )).' %';
                                            }else{
                                                echo '0,00 %';
                                        }?>
                                    </span>
                                </td>
                                <td class="display-external-consumed">
                                    <span class="display-value">
                                        <?php
                                            echo $this->Number->format(round($consumedExter['ActivityTask']['consumedex'],2), array(
                                                    'places' => 2,
                                                    'before' => ' ',
                                                    'escape' => false,
                                                    'decimals' => ',',
                                                    'thousands' => ' '
                                                ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                                <td class="display-external-remain">
                                    <span class="display-value">
                                        <?php
                                            echo $this->Number->format(round($remainExter, 2), array(
                                                    'places' => 2,
                                                    'before' => ' ',
                                                    'escape' => false,
                                                    'decimals' => ',',
                                                    'thousands' => ' '
                                                ));
                                        ?>
                                    </span>
                                    <?php echo __('M.D', true);?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                 </div><br clear="all"  />
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;" class="slick-pager">

                </div>
            </div></div></div>
            <?php echo $this->element('grid_status'); ?>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php
$i18n = array(
    //for extjs
    'New Task' => __('New Task', true),
    'Update' => __('Update', true),
    'Resources' => __('Resources', true),
    'Filter' => __('Filter', true),
    'Save' => __('Save', true),
    'Cancel' => __('Cancel', true),
    'New Task' => __('New Task', true),
    'Create not-continuous task' => __('Create not-continuous task', true),
    'Warning' => __('Warning', true),
    'Workload' => __('Workload', true),
    'Delete task and its sub-tasks?' => __('Delete task and its sub-tasks?', true),
    'Workload Detail For Employee And Profit Center' => __('Workload Detail For Employee And Profit Center', true),
    'Delete' => __('Delete', true),
    'Total workload' => __('Workload', true),
    'ok' => __('OK', true),
    'yes' => __('Yes', true),
    'no' => __('No', true),
    'cancel' => __('Cancel', true),
    'Reset' => __('Reset', true),
    'Task name already existed' => __('Task name already existed', true),
    'Edit' => __('Edit', true),
    'No consumed and the current date > start date' => __('No consumed and the current date > start date', true),
    'Consumed > Workload' =>  __('Consumed > Workload', true),
    'Task not closed and the current date > end date' => __('Task not closed and the current date > end date', true),
    'Keep the duration of the task ' => __('Keep the duration of the task ', true)
);
    $i18n = array_merge($i18n, $this->requestAction('/translations/getByLang/Project_Task'));
?>
<script>
    var webroot = <?php echo json_encode($this->Html->url('/')) ?>;
    var i18n_text = <?php echo json_encode($i18n) ?>;
    var showProfile = <?php echo isset($companyConfigs['activate_profile']) ? (string) $companyConfigs['activate_profile'] : '0' ?>;
    var is_manual_consumed = <?php echo isset($companyConfigs['manual_consumed']) ? (string) $companyConfigs['manual_consumed'] : '0' ?>;
    var gap_linked_task = <?php echo isset($companyConfigs['gap_linked_task']) ? (string) $companyConfigs['gap_linked_task'] : '0' ?>;
    var columns_order = <?php echo json_encode($this->requestAction('/admin_task/getTaskSettings')) ?>;
    var hightlightTask = <?php echo json_encode(isset($_GET['id']) ? $_GET['id'] : '') ?>;
    var manual;
    function i18n(text){
        if( typeof i18n_text[text] != 'undefined' )return i18n_text[text];
        return text;
    }
</script>

<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo Configure::read('Config.language'); ?>" id="language"  />
<input type="hidden" value="<?php echo $this->Session->read('Auth.employee_info.Role.name'); ?>" id="pm_acl" />
<input type="hidden" value="<?php echo $activityName['Activity']['is_freeze']; ?>" id="show_freeze" />
<input type="hidden" value="<?php echo $settingP['ProjectSetting']['show_freeze']; ?>" id="is_show_freeze" />
<input type="hidden" value="<?php echo $activityName['Activity']['off_freeze']; ?>" id="off_freeze" />
<textarea style="display:none" id="priorityJson"><?php echo $listPrioritiesJson; ?></textarea>
<script type="text/javascript">
    var canModify = true;
	var budgetCurrency = <?php echo json_encode($budget_settings)?>;
    $('#update-status').hide();
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 400) ? 400 : heightTable;
    wdTable.css({
        height: heightTable,
    });
    var xdelay;
    var isFull = false;
    $(window).resize(function(){
        clearTimeout(xdelay);
        xdelay = setTimeout(function(){
            var treepanel = Ext.getCmp('<?php echo $ID ?>');
            if( treepanel ){
                if( !isFull ){
                    heightTable = $(window).height() - wdTable.offset().top - 40;
                    //heightTable = (heightTable < 400) ? 400 : heightTable;
                    wdTable.css({
                        height: heightTable,
                    });
                    treepanel.setWidth(wdTable.width());
                    treepanel.setHeight(heightTable);
                } else {
                    var sh = $(window).height(),
                        sw = $(window).width();
                    wdTable.css({
                        height: sh
                    });
                    treepanel.setWidth(sw);
                    treepanel.setHeight(sh);
                }
                treepanel.updateLayout();
            }
        }, 200);
    });
    $('#dialog_import_CSV').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        height      : 150
    });
    $('#dialog_data_CSV').dialog({
        position    :'top',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        minHeight   : 102,
        width       : 760
        //auto  : true
        // height      : 230
    });
    $("#import_CSV").click(function(){
        $('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[csv_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
        $('#dialog_import_CSV').dialog("open");

    });
    $("#import-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[csv_file_attachment]']").val()){
            var filename = $("input[name='FileField[csv_file_attachment]']").val();
            var valid_extensions = /(\.csv)$/i;
            if(valid_extensions.test(filename)){
                $('#uploadForm').submit();
            }
            else{
                $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error');
            }
            $("#dialog_import_CSV").dialog("close");
        }else{
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error');
        }
    });
    /* table .end */
    var createDialog = function(){
        $('#dialog_skip_value').dialog({
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
    $(".cancel").live('click',function(){
        $("#dialog_data_CSV").dialog("close");
        $("#dialog_import_CSV").dialog("close");
    });
    clearInterval(flag);
    var ID = <?php echo json_encode($ID) ?>;
	var flag=setInterval(function(){
		if($('#' + ID + '-body').find('div table').height()>0){
			$('#loading').remove();
            var check = parseFloat($('.internal-line .display-var span.display-value').text());
            if(check>0){
                $('.internal-line .display-var span.display-value').removeClass('var-green');
                $('.internal-line .display-var span.display-value').addClass('var-red');
            }else{
                $('.internal-line .display-var span.display-value').removeClass('var-red');
                $('.internal-line .display-var span.display-value').addClass('var-green');
            }
            var checkEx = parseFloat($('.external-line .display-var span.display-value').text());
            if(checkEx>0){
                $('.external-line .display-var span.display-value').removeClass('var-green');
                $('.external-line .display-var span.display-value').addClass('var-red');
            }else{
                $('.external-line .display-var span.display-value').removeClass('var-red');
                $('.external-line .display-var span.display-value').addClass('var-green');
            }
            var treepanel = Ext.getCmp(ID);
            treepanel.setHeight(Ext.getBody().getViewSize().height);
            var fr = treepanel.getSetting('show_type');
            $('.type-' + fr).addClass('type-focus');
            $('.filter-type').unbind('click');
            $('.filter-type').on('click', function(){
                var which = $(this).data('type');
                if( $(this).hasClass('type-focus') ){
                    $('.filter-type').removeClass('type-focus');
                    which = '';
                } else {
                    $('.filter-type').removeClass('type-focus');
                    $(this).addClass('type-focus');
                }
                treepanel.saveSetting('show_type', which);
                treepanel.addTypeFilter(which, 1);
            });
			clearInterval(flag);
		}
	},1000);
    // clearInterval(flagActivity);
    // var flagActivity=setInterval(function(){
    //     if($('#pmstreepanelactivity-body').find('div table').height()>0){
    //         $('#loading').remove();
    //         var check = parseFloat($('.internal-line .display-var span.display-value').text());
    //         if(check>0){
    //             $('.internal-line .display-var span.display-value').removeClass('var-green');
    //             $('.internal-line .display-var span.display-value').addClass('var-red');
    //         }else{
    //             $('.internal-line .display-var span.display-value').removeClass('var-red');
    //             $('.internal-line .display-var span.display-value').addClass('var-green');
    //         }
    //         var checkEx = parseFloat($('.external-line .display-var span.display-value').text());
    //         if(checkEx>0){
    //             $('.external-line .display-var span.display-value').removeClass('var-green');
    //             $('.external-line .display-var span.display-value').addClass('var-red');
    //         }else{
    //             $('.external-line .display-var span.display-value').removeClass('var-red');
    //             $('.external-line .display-var span.display-value').addClass('var-green');
    //         }
    //         var treepanel = Ext.getCmp("pmstreepanelactivity");
    //         // treepanel.setHeight(Ext.getBody().getViewSize().height);
    //         $('.filter-type').unbind('click');
    //         $('.filter-type').on('click', function(){
    //             treepanel.saveSetting('show_type', $(this).data('type'));
    //             treepanel.filterByStatus(treepanel.getSetting('status_filter', {}), $(this).data('type'));
    //         });
    //         clearInterval(flagActivity);
    //     }
    // },1000);
</script>
<!--MODIFY BY VINGUYEN 18/06/2014-->
<div id="showdetail">
    <div id="gs-popup-header">
        <div class="gs-header-title">
            <ul>
                <li><a href="javascript:;" id="filter_date"><?php echo __("Date", true)?></a></li>
                <!--li><a href="" id="filter_week"><?php echo __("Week", true)?></a></li-->
                <li><a href="javascript:;" id="filter_month"><?php echo __("Month", true)?></a></li>
                <li><a href="javascript:;" id="filter_year"><?php echo __("Year", true)?></a></li>
            </ul>
            <p class="gs-name-header"><?php __('Availability');?> : <span>employee</span></p>
        </div><br clear="all"  />
        <div class="gs-header-content">
            <p class="gs-header-content-name"><?php __('Task');?> : <span>task</span></p>
            <p class="gs-header-content-start"><?php __('Start Date');?> : <span>start</span></p>
            <p class="gs-header-content-end"><?php __('End Date');?> : <span>end</span></p>
            <p class="gs-header-content-work"><?php __('Workload');?> : <span>workload</span></p>
            <p class="gs-header-content-avai"><?php __('Availability');?> : <span>avai</span></p>
            <p class="gs-header-content-over"><?php __('Overload');?> : <span>over</span></p>
        </div><br clear="all"  />
    </div>
    <div id="gs-popup-content">
        <div class="table-left">
            <table id="tb-popup-content">
                <tr class="popup-header">
                    <td style="width: 450px;">&nbsp;</td>
                    <td style="width: 90px;"><div class="text-center"><?php __('Priority');?></div></td>
                    <td style="width: 60px;"><div class="text-center"><?php __('Total');?></div></td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Working day');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-working" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Absence');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-vacation" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Capacity');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-capacity" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Workload');?></div></td>
                    <td>&nbsp;</td>
                    <td id="total-workload" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Availability');?> * </div></td>
                    <td>&nbsp;</td>
                    <td id="total-availability" class="text-right">&nbsp;</td>
                </tr>
                <tr>
                    <td class="popup-header-group"><div><?php __('Overload');?> </div></td>
                    <td>&nbsp;</td>
                    <td id="total-overload" class="text-right">&nbsp;</td>
                </tr>
                <tbody class="popup-task-detail">

                </tbody>
            </table>
        </div>
        <div class="table-right">
            <table id="tb-popup-content-2">
                <tbody class="popup-header-2">
                </tbody>

                <tbody class="popup-working-2">
                </tbody>

                <tbody class="popup-vaication-2">
                </tbody>

                <tbody class="popup-capacity-2">
                </tbody>

                <tbody class="popup-workload-2">
                </tbody>

                <tbody class="popup-availa-2">
                </tbody>

                <tbody class="popup-over-2">
                </tbody>

                <tbody class="popup-task-detail-2">
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--END-->
<?php echo $this->Html->script(array(
    'extjs/ext5-include',
    //'extjs/extjs/ext-theme-neptune',
    'extjs/app/app',
    'jquery-ui.multidatespicker',
    'jquery.multiSelect'
)); ?>
<?php echo $this->Html->css(array(
    // 'extjs/resources/css/ext-all',
    // 'extjs/resources/css/ext-neptune',
    //'extjs/resources/css/ext-all-neptune',
    // 'extjs/resources/css/tasks',
    'extjs/resources/css/ext-custom',
    'jquery.multiSelect'
)); ?>
<script type="text/javascript">
    $('#ActivityOffFreeze').click (function(){
          var thisCheck = $(this);
          if(thisCheck.is(':checked')) {
            $.post("<?php echo $this->Html->url('/activity_tasks/update_initial/'.$activity_id.'/1');?>", function(data){
                location.reload();
            });
          }else{
            $.post("<?php echo $this->Html->url('/activity_tasks/update_initial/'.$activity_id.'/0');?>", function(data){
                location.reload();
            });
          }
    });
    $('#export-submit').click(function(){
        $('#export-item-list').closest('form').submit();
    });
    var freeze =  $('ul#displayFreeze li.unfreeze-freeze').width();
    var widthF = parseFloat(freeze)+35;
    if ($('ul#displayFreeze li').length > 1) {
        widthF = parseFloat(freeze) + 75;
    }
    $('ul#displayFreeze').css('width',widthF);
//EXPAND TREE
	$(document).keyup(function(e) {
		if (window.event)
		{
			var value = window.event.keyCode;
		}
		else
			var value=e.which;
		if (value == 27) { collapseTaskScreen(); }
	});
	function collapseTaskScreen()
	{
		$('#collapse').hide();
		$('#project_container').removeClass('treeExpand');
        isFull = false;
        $(window).trigger('resize');
	}
	function expandTaskScreen()
	{
		$('#project_container').addClass('treeExpand');
		$('#collapse').show();
        isFull = true;
        $(window).trigger('resize');
	}
	function filterEmployee(text,e){
		$('li[rel="li-employee"]').each(function(index, element) {
			str=$(this).html();
			str=str.toLowerCase();
			text=text.toLowerCase();
			elm=$(this).attr('id');
			if(str.indexOf(text)==-1)
			{
				$(this).hide();
				$('div[rel='+elm+']').hide();
			}
			else
			{
				$(this).show();
				$('div[rel='+elm+']').show();
			}

        });
	}
// special task section
    //init state
    var listDeletion = [];
    var Task;
    var Holidays = {};  //get by ajax
    var Workdays = <?php echo json_encode($workdays) ?>;
    var startDate, endDate;

    var monthName = <?php
        echo json_encode(array(
            __('January', true),
            __('February', true),
            __('Marsh', true),
            __('April', true),
            __('May', true),
            __('June', true),
            __('July', true),
            __('August', true),
            __('September', true),
            __('October', true),
            __('November', true),
            __('December', true)
        ));
    ?>;

    Date.prototype.format = function(format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, this);
    }

    function SpecialTask(options){
        this.defaults = {
            data: {},
            columns: [],
            id: 0,
            task: {
                id: 0,
                task_title: '',
                task_priority_id: '',
                task_status_id: '',
                task_start_date: '',
                task_end_date: '',
                project_planed_phase_id: 0,
                profile_id: 0,
                manual_consumed: 0
            },
            consume: {}
        };
        this.options = $.extend({}, this.defaults, options);
        // this.data = {};
        // this.columns = [];
        // this.deleted = [];
        this.init();
        return this;
    }
    SpecialTask.prototype.init = function(){
        listDeletion = [];
        var me = this;
        //init data
        $('#nct-id').val(me.options.id);
        $('#nct-phase-id').val(me.options.task.project_planed_phase_id);
        $('#nct-priority option[value="' + me.options.task.task_priority_id + '"]').prop('selected', true);
        $('#nct-status option[value="' + me.options.task.task_status_id + '"]').prop('selected', true);
        $('#nct-start-date').val(me.options.task.task_start_date).datepicker( "setDate", me.options.task.task_start_date );
        $('#nct-end-date').val(me.options.task.task_end_date).datepicker( "setDate", me.options.task.task_end_date );
        $('#nct-manual').val(me.options.task.manual_consumed);

        $('#nct-profile option[value="' + me.options.task.profile_id + '"]').prop('selected', true);

        $('#nct-name').val(me.options.task.task_title);
        this.columns = this.options.columns;
        if( typeof this.options.data == 'function' ){
            this.data = this.options.data();
        } else {
            this.data = this.options.data;
        }
        var columns = this.columns;
        var data = this.data;
        var request = this.options.request;

        //build html
        var estimate = {}, consume, inUsed;
        try{
            consume = parseFloat(this.options.request.all[0]);
            inUsed = parseFloat(this.options.request.all[1]);
        } catch(ex){
            consume = 0;
            inUsed = 0;
        } finally {
            if( isNaN(consume) )consume = 0;
            if( isNaN(inUsed) )inUsed = 0;
        }
        /*
        *{ id : number }
        */
        //build header
        var list = [];
        $.each(columns, function(i, col){
            var html = '<td class="value-cell header-cell cell-' + col.id + '" id="col-' + col.id + '">' + col.name + '</td>';
            $(html).insertBefore('#abcxyz');
            //hide from select
            $('#res-' + col.id).hide();
            //build footer
            html = '<td class="value-cell cell-' + col.id + '" id="foot-' + col.id + '"></td>';
            $(html).insertBefore('#total-consumed');
            estimate[col.id] = 0;
            //update assign list
            $('#check-' + col.id).prop('checked', true).parent().addClass('checked');
            // if( typeof me.options.consume[col.id] != 'undefined' && me.options.consume[col.id] ){
            //     $('#check-' + col.id).prop('disabled', true);
            // }
            list.push(col.name);
        });

        $('#nct-resources-list > span').html(list.length ? list.join(', ') : '&nbsp;');
        var type = 0;
        //build data
        $.each(data, function(row, items){
            //build row
            var date = row.substr(2);
            var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: right">' + toRowName(row) + '<span class="cancel" onclick="removeRow(this)" href="javascript:;"></span></td>';

            var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
            if( isNaN(c) )c = 0;
            if( isNaN(iu) )iu = 0;
            $.each(items, function(i, item){
                var id = item.reference_id + '-' + item.is_profit_center;
                html += '<td class="value-cell cell-'+id+'"><input type="text" id="val-' + date + '-' + id + '" data-old="' + item.estimated + '" class="workload-'+id+'" value="' + item.estimated + '" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>';
                //calculate estimated
                estimate[id] += item.estimated;
                //c += item.consumed;
//                consume += item.consumed;
//                if( typeof item.inUsed != 'undefined' ){
//                    inUsed += item.inUsed;
//                    iu += item.inUsed;
//                }
                type = item.type;
            });
            //last col
            html += '<td style="background: #f0f0f0" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td></tr>';
            $('#assign-table tbody').append(html);
            if( c > 0 || iu > 0 ){
                $('#date-' + date).find('.cancel').remove();
            }
            //consume += c;
//            inUsed += iu;
        });
        //fill data for footer
        $.each(columns, function(i, col){
            $('#foot-' + col.id).text(estimate[col.id].toFixed(2));
        });
        $('#total-consumed').text(consume.toFixed(2) + ' (' + inUsed.toFixed(2) + ')');
        //disable name if task have consumed/in used
        if( consume > 0 || inUsed > 0 ){
            $('#nct-name').prop('disabled', false);
        //2015-06-29
            //disable neu co consume/in used
            $('#nct-range-type').prop('disabled', true);
        } else {
            $('#nct-range-type').prop('disabled', false);
        }
        if( consume > 0 || inUsed > 0 || $('#nct-range-type').val() == '0' ){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        //chon range tai day:
        $('#nct-range-type option[value="' + type +'"]').prop('selected', true);
        selectRange();
        //calculate
        calculateTotal();
        //refresh picker
        refreshPicker();
        //
        bindNctKeys();
        //done!
    }
    SpecialTask.prototype.destroy = function(){
        listDeletion = [];
        this.options = {
            data: {},
            columns: [],
            id: 0,
            task: {
                id: 0,
                task_title: '',
                task_priority_id: '',
                task_status_id: '',
                task_start_date: '',
                task_end_date: '',
                project_planed_phase_id: 0,
                profile_id: 0,
                manual_consumed: 0
            },
            consume: {}
        };
        this.data = {};
        this.columns = [];
        //reset input
        $('.w .text').val('').prop('disabled', false);
        //reset selection
        $('.w select option').show().filter('[value=""]').prop('selected', true);
        //reset table
        $('#assign-table tbody').html('');
        $('#assign-table td.value-cell').remove();
        $('#total-consumed').html('0');
        $('#nct-resources-list + .multiSelectOptions input').prop('checked', false).prop('disabled', false).parent().removeClass('checked');
        $('#nct-resources-list > span').html('&nbsp;');
        $('#nct-range-type option[value="0"]').prop('selected', true);
        //$('#add-date').addClass('disabled');
    }
    var project_id = <?php echo isset($projectName['Project']['id']) ? $projectName['Project']['id'] : 0 ?>;
    var activity_id = <?php echo $activityName['Activity']['id'] ?>;
    SpecialTask.prototype.getUrl = function(){
        return '<?php echo $this->Html->url('/') ?>' + (project_id != 0 ? 'project_tasks' : 'activity_tasks') + '/saveNcTask';
    }
    SpecialTask.prototype.commit = function(){
        //check data
        var name = $.trim($('#nct-name').val()),
            sd = $('#nct-start-date').datepicker('getDate'),
            ed = $('#nct-end-date').datepicker('getDate');
        if( !name ){
            alert('<?php __('Error: Please enter task name') ?>');
            return false;
        }
        if( !sd || !ed ){
            alert('<?php __('Error: Please pick start date / end date') ?>');
            return false;
        }
        if( sd > ed ){
            alert('<?php __('Error: start date can not be greater than end date') ?>');
            $('#nct-start-date').focus();
            return false;
        }
        //check date range
        if( !isValidList() ){
            alert('<?php __('Error: There are dates not between start date and end date') ?>');
            return false;
        }
        var result = {
            data: {
                workloads: {},
                id: <?php echo isset($projectName['Project']['id']) ? $projectName['Project']['id'] : 0 ?>,
                <?php if( !isset($projectName['Project']['id']) ): ?>activity_id : <?php echo $activityName['Activity']['id'] ?>,<?php endif ?>
                d: listDeletion,
                type: $('#nct-range-type').val(),
                task: {
                    id: $('#nct-id').val(),
                    task_title: name, //$('#nct-name').prop('disabled') ? '' : name,
                    task_priority_id: $('#nct-priority').val(),
                    task_status_id: $('#nct-status').val(),
                    task_start_date: $('#nct-start-date').val(),
                    task_end_date: $('#nct-end-date').val(),
                    project_planed_phase_id: $('#nct-phase-id').val(),
                    profile_id: $('#nct-profile').val(),
                    manual_consumed: $('#nct-manual').val()
                }
            }
        };

        $count_value_cell_hidden = $('#assign-table tbody tr .value-cell').filter(function() {
		    return $(this).css('display') == 'none';
		}).length;
        if( !$('#assign-table tbody tr .value-cell input').length ){
            alert('<?php __('Select at least a resource or a team') ?>');
            return false;
        } else if( $count_value_cell_hidden == $('#assign-table tbody tr .value-cell input').length ) {
			alert('<?php __('Select at least a resource or a team') ?>');
            return false;
		}

        $('#assign-table tbody tr').each(function(){
            var tr = $(this),
                date = tr.find('.nct-date').prop('id').replace('date-', '');
            result.data.workloads[date] = [];
            var inputs = tr.find('.value-cell input:visible');
            inputs.each(function(){
                var me = $(this),
                    id = me.prop('class').replace('workload-', ''), t = id.split('-');
                result.data.workloads[date].push({
                    reference_id: t[0],
                    estimated: me.val(),
                    is_profit_center: t[1]
                });
            });
        });
        //console.log(result.data.workloads);return;
        //save
        //disable button first
        var btns = $('#save-special,#cancel-special').addClass('disabled');
        var text = $('#nct-progress').show();

        var tree = $('#special-task-info').data('tree');
        $.ajax({
            url: Task.getUrl(),
            type: 'POST',
            dataType: 'json',
            data: result,
            success: function(response){
                $('#special-task-info').dialog('close');
                tree.setLoading('Please wait');
                /*
                * add task
                */
                if( response.result ){
                    if( result.data.task.id == 0 ){
                        var
                            cellEditingPlugin   = tree.cellEditingPlugin, // for double click
                            selectionModel      = tree.getSelectionModel(),
                            selectedTask        = selectionModel.getSelection()[0],
                            parentTask          = selectedTask;
                        if(!selectedTask){
                            selectionModel.select(0);
                            parentTask = selectionModel.getSelection()[0];
                        }
                        var task = $.extend({}, {
                            task_title  : '',
                            loaded      : true,
                            expanded    : true,
                            children    : [],
                            parent_id   : 0,
                            parentId    : 0,
                            leaf     : true,
                            // parent_name : parentTask.get('task_title'),
                            // project_id  : tree.project_id ? tree.project_id : 0,
                            // activity_id : tree.activity_id ? tree.activity_id : 0,
                            is_new      : true,
                            is_nct      : 1
                        }, response.data);
                        if( project_id )var newTask = Ext.create('PMS.model.ProjectTask', task);
                        else var newTask = Ext.create('PMS.model.ActivityTask', task);
                        parentTask.set('leaf', false);
                        parentTask.appendChild(newTask);

                        if(!parentTask.data.children||parentTask.data.children=='null')
                            parentTask.data.children=[];
                        parentTask.data.children.push(newTask.data);

                        var eAe = function() {
                            //temporary clear filter
                            // tree.clearFilter();
                            if(parentTask.isExpanded()) {
                                selectionModel.select(newTask);
                                //cellEditingPlugin.startEdit(newTask, 0);
                            } else {
                                tree.on('afteritemexpand', function startEdit(task) {
                                    if(task === parentTask) {
                                        selectionModel.select(newTask);
                                        //cellEditingPlugin.startEdit(newTask, 0);
                                        tree.un('afteritemexpand', startEdit);
                                    }
                                });
                                parentTask.expand();
                            }
                        };
                        if(tree.getView().isVisible(true)) {
                            eAe();
                        } else {
                            tree.on('expand', function onExpand() {
                                expandAndEdit();
                                tree.un('expand', onExpand);
                            });
                            tree.expand();
                        }
                    }

                    tree.refreshSummary(function(callback){
                       tree.setLoading(false);
                    });

                    tree.refreshStaffing(function(callback){
                        //do nothing
                    });

                    tree.refreshView();
                } else {
                    alert('<?php __('Error saving task. Please reload the page') ?>');
                }
            },
            complete: function(){
                btns.removeClass('disabled');
                text.hide();
                //close dialog
                $('#special-task-info').dialog('close');
                //excute
            }
        })
    }
    function bindNctKeys(){
        var length = $('[data-ref]').length;
        $('[data-ref]').off('focus').on('focus', function(){
            var f = $(this).data('focused');
            if( f )return;
            $(this).data('focused', 1);
            $(this).select();
        }).on('blur', function(){
            $(this).data('focused', 0);
            // changeTotal(this);
        })
        .off('keydown').on('keydown', function(e){
            //tab key
            var index = $('[data-ref]').index(this);
            if( e.which == 9 ){
                $(this).next('[data-ref]').focus();
            }
        });
    }
    function resetPicker(){
        if( $('#nct-range-type').val() == 0 ){
            $('#date-list').multiDatesPicker('resetDates');
            $('.nct-date').each(function(){
                var value = $(this).text();
                $('#date-list').multiDatesPicker('addDates', [$.datepicker.parseDate('dd-mm-yy', value)]);
            });
        }
    }

    function removeRow(e){
        //check if has in used / consumed
        if( $(e).parent().parent().find('.ciu-cell').text() != '0.00 (0.00)' ){
            alert('<?php __('Can not delete this because it has consumed/in used data') ?>');
            return;
        }
        if( confirm('<?php __('Are you sure?') ?>') ){
            var cols = $(e).parent().parent().find('input');
            cols.each(function(){
                var me = $(this);
                var id = me.prop('class').replace('workload-', '');
                var original = parseFloat($('#foot-' + id).text());
                $('#foot-' + id).text((original - parseFloat(me.val())).toFixed(2));
            });
            $(e).parent().parent().remove();
            //add to deletion list
            listDeletion.push('date:' + $(e).parent().parent().find('.nct-date').text());
            refreshPicker();
        }
    }

    function changeTotal(e){
        //check here
        var me = $(e);
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#nct-range-type').val());
        if( (isNaN(newVal) || newVal < 0 ) || (type == 0 && newVal > 1) ){
            // if(  type == 0 )
            //     alert('<?php __('Enter value between 0 and 1') ?>');
            // else alert('<?php __('Please enter value >= 0') ?>');
            me.val(old);
            me.focus();
            return;
        }
        var id = me.prop('class').replace('workload-', '');
        var total = 0;
        $('.' + me.prop('class')).each(function(){
            total += parseFloat($(this).val());
        });
        $('#foot-' + id).text(total.toFixed(2));
        me.data('old', newVal);
        calculateTotal();
    }

    function calculateTotal(){
        var total = 0;
        $('#assign-list tfoot .value-cell').each(function(){
            total += parseFloat($(this).text());
        });
        $('#nct-total-workload').val(total.toFixed(2));
    }

    function minMaxDate(){
        var min, max;
        var type = $('#nct-range-type').val();
        if( type != 0 ){
            return [$('#nct-start-date').datepicker('getDate'), $('#nct-end-date').datepicker('getDate')];
        }
        $('.nct-date').each(function(){
            var text = $(this).text();
            var value = $.datepicker.parseDate('dd-mm-yy', text);
            if( !min || min > value)
                min = value;
            if( !max || max < value)
                max = value;
        });
        return [min, max];
    }


    function isValidList(){
        var range = minMaxDate(),
            start = $('#nct-start-date').datepicker('getDate'),
            end = $('#nct-end-date').datepicker('getDate');
        if( range[0] < start || range[1] > end )return false;
        return true;
    }

    //new enhancement 2015/06/29
    function selectCurrentRange(){
        //$('#nct-range-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active');
    }
    function unhightlightRange(){
        $('#nct-range-picker').find('.ui-state-highlight').removeClass('ui-state-highlight');
        //$('#nct-range-picker').find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    }

    function resetRange(){
        var start = $('#nct-start-date').datepicker('getDate');
        if( start ){
            //reset range picker
            $('#nct-range-picker').datepicker('setDate', start);
        }
        unhightlightRange();
        startDate = null;
        endDate = null;
    }

    function selectRange(){
        var val = parseInt($('#nct-range-type').val());
        switch(val){
            case 1:
            case 2:
                $('.start-end').hide();
                $('.range-picker').show();
            break;
            default:
                $('.start-end').show();
                $('.range-picker').hide();
            break;
        }
        if( val == 0 ){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        resetRange();
        if( !$('#assign-table tbody tr').length ){
            $('#assign-list tfoot .value-cell').text('0.00');
            $('#nct-total-workload').val('0.00');
        }
    }

    function dateDiff(date1, date2) {
        date1.setHours(0);
        date1.setMinutes(0, 0, 0);
        date2.setHours(0);
        date2.setMinutes(0, 0, 0);
        var datediff = Math.abs(date1.getTime() - date2.getTime()); // difference
        return parseInt(datediff / (24 * 60 * 60 * 1000), 10); //Convert values days and return value
    }

    function dateString(date, format){
        if( !format )format = 'dd-mm-yy';
        return $.datepicker.formatDate(format, date);
    }

    function toRowName(date){
        //parse date from task
        var part = date.split('_');
        switch(part[0]){
            case '1':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                d = part[2].split('-');
                var end = new Date(d[2], d[1]-1, d[0]);
                return dateString(start, 'dd/mm') + ' - ' + dateString(end, 'dd/mm/yy');
            case '2':
                var d = part[1].split('-');
                var start = new Date(d[2], d[1]-1, d[0]);
                return monthName[start.getMonth()] + ' ' + d[2];
            default:
                return dateString(new Date(part[1]));
        }
    }

    $(document).ready(function(){
        $('#clean-filters').click(function(){
            try {
                var panel = Ext.getCmp('pmstreepanelactivity');
                panel.cleanFilters();
            } catch(ex){
            }
        });
        $('#special-task-info').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 'auto',
            height      : 'auto',
            close       : function(){
                if( Task )Task.destroy();
            }
        });
        //$('#assign-list').height(Math.round((50*$(window).height())/100));
        $('#nct-start-date').datepicker({
            //changeMonth: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                return [Workdays[date] == 1, '', ''];
            },
            onSelect: function(){
                //showPicker();
                refreshPicker();
            }
        }).prop('readonly', true);
        $('#nct-end-date').datepicker({
            //changeMonth: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-start-date').datepicker('getDate'),
                    check = Workdays[date] == 1;
                if( start != null ){
                    check = check && start <= d;
                }
                return [ check, '', ''];
            },
            beforeShow: function(e, i){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $(e).datepicker('getDate');
                if( !end && start ){
                    return {defaultDate: start};
                }
            },
            onSelect: function(){
                //showPicker();
                refreshPicker();
            }
        }).prop('readonly', true);

        $('#add-date').click(function(){
            if( $(this).hasClass('disabled') )return;
            var dates = getValidDate();
            //check neu co assign thi moi them date
            if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length == 0 ){
                return;
            }
            for(var i in dates){
                var date = dates[i];
                if( date ){
                    var invalid = false;
                    $('.nct-date').each(function(){
                        var value = $(this).text();
                        if( value == date )invalid = true;
                    });
                    if( !invalid ){
                        //add new row
                        var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: right">' + date + '<a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
                        $('.header-cell').each(function(){
                            var col = $(this);
                            var id = col.prop('id').replace('col-', '');
                            var hide = '';
                            if( !col.is(':visible') )hide = 'style="display: none"';
                            html += '<td class="value-cell cell-' + id + '" ' + hide + '><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload-' + id + '" value="0" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>';
                        });
                        html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td></tr>';
                        $('#assign-table tbody').append(html);
                    }
                }
            }
            bindNctKeys();
            //$('#date-list').multiDatesPicker('resetDates');
        });

        $('#nct-reset-date').click(function(){
            refreshPicker();
        });

        $('#nct-resources-list').multiSelect({
            selectAll: false,
            noneSelected: '&nbsp;',
            oneOrMoreSelected: '*'
        });
        //assign: TODO: add column to the right before consume
        $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox').click( function() {
            var me = $(this);
            var name = me.parent().text(),
                id = me.val();
            var cell = $('.cell-' + id);
            //add
            if( me.prop('checked') ){
                if( cell.length ){
                    cell.show();
                    return;
                }
                //add header
                var html = '<td class="value-cell header-cell cell-' + id + '" id="col-' + id + '">' + name + '<a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
                $(html).insertBefore('#abcxyz');
                //add content
                $('.nct-date').each(function(){
                    var ciu = $(this).parent().find('.ciu-cell'),
                        date = $(this).text();
                    $('<td class="value-cell cell-' + id + '"><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload-' + id + '" value="0" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>').insertBefore(ciu);
                });
                //add footer
                $('<td class="value-cell cell-' + id + '" id="foot-' + id + '">0.00</td></tr>').insertBefore('#total-consumed');
                bindNctKeys();
            } else {
                //remove (or hide), i choose to hide those cells
                cell.hide();
            }
            refreshPicker();
        });

        $('#cancel-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            $('#special-task-info').dialog('close');
        });
        $('#save-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            Task.commit();
        });
        //new enhancement

        //$('#nct-range-picker .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); }).live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });

        $('#nct-range-picker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                var curStart = $('#nct-start-date').datepicker('getDate'),
                    curEnd = $('#nct-end-date').datepicker('getDate');
                if( type == 1 ){
                    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
                    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
                } else {
                    startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
                    endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
                }
                if( curStart > startDate ){
                    $('#nct-start-date').datepicker('setDate', startDate);
                }
                if( curEnd < endDate ){
                    $('#nct-end-date').datepicker('setDate', endDate);
                }
                var start = dateString(startDate);
                var end = dateString(endDate);
                selectCurrentRange();
            },
            beforeShowDay: function(date) {
                var cssClass = '';
                var canSelect = true;
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');

                if( !start || !end )canSelect = false;
                else canSelect = date >= start && date <= end;

                if(date >= startDate && date <= endDate)
                    cssClass = 'ui-state-highlight';
                return [canSelect, cssClass];
            },
            onChangeMonthYear: function(year, month, inst) {
                selectCurrentRange();
            }
        });
        $('#nct-range-type').change(function(){
            //xoa het tat ca workload
            $('#assign-table tbody').html('');
            selectRange();
        });
        $('#add-range').click(function(){
            //check neu co assign thi moi them date
            if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length == 0 ){
                return;
            }
            if( startDate && endDate ){
                _addRange(startDate, endDate);
            }
        });
        $('#create-range').click(function(){
            if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length == 0 ){
                return;
            }
            var dateType = parseInt($('#nct-range-type').val());
            //available for week and month
            if( dateType != 0 ){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                addRange(start, end);
            }
        });
        $('#fill-workload').click(function(){
            var val = parseFloat($('#per-workload').val());
            if( isNaN(val) )return;
            if( $('#nct-range-type').val() == '0' && val > 1 )return;
            $('#assign-table tbody .value-cell input').each(function(){
                $(this).val(val).data('old', val).trigger('change');
            });
            //calculateTotal();
        });
    });
/*
    @date: js date object
    @day:
        0 = sunday
        1 = monday..
        6 = saturday
    @return: new date object
*/
    function getDayOfWeek(date, day) {
        var d = new Date(date),
            cday = d.getDay(),
            diff = d.getDate() - cday + day;
        return new Date(d.setDate(diff));
    }
    function getListWeek(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var monday = getDayOfWeek(current, 1),
                currentDate = monday.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: monday,
                    end: getDayOfWeek(current, 5)
                };
            }
            current = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+7);
        }
        return list;
    }
    function getListMonth(start, end){
        var list = {};
        var current = new Date(start);
        end = new Date(end.getFullYear(), end.getMonth() + 1, 0);
        while( current <= end ){
            current.setDate(1);
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    start: sm,
                    end: new Date(sm.getFullYear(), sm.getMonth() + 1, 0)
                };
            }
            current.setMonth(current.getMonth() + 1);
        }
        return list;
    }
    function addRange(start, end, reset){
        if( reset ){
            //xoa het tat ca workload
            $('#assign-table tbody').html('');
            selectRange();
        }
        var type = parseInt($('#nct-range-type').val());
        if( type == 0 )return;
        var list = type == 1 ? getListWeek(start, end) : getListMonth(start, end),
            minDate, maxDate;
        $.each(list, function(key, date){
            if( !minDate || minDate >= date.start ){
                minDate = new Date(date.start);
            }
            if( !maxDate || maxDate <= date.end ){
                maxDate = new Date(date.end);
            }
            _addRange(date.start, date.end);
        });
        $('#nct-start-date').datepicker('setDate', minDate);
        $('#nct-end-date').datepicker('setDate', maxDate);
    }
    function _addRange(start, end){
        var date = dateString(start) + '_' + dateString(end);
        var type = parseInt($('#nct-range-type').val());
        var rowName = type == 2 ? monthName[start.getMonth()] + ' ' + start.getFullYear() : start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        var invalid = $('#date-' + date).length ? true : false;
        if( !invalid ){
            //add new row
            var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: right">' + rowName + '<a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
            $('.header-cell').each(function(){
                var col = $(this);
                var id = col.prop('id').replace('col-', '');
                var hide = '';
                if( !col.is(':visible') )hide = 'style="display: none"';
                html += '<td class="value-cell cell-' + id + '" ' + hide + '><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload-' + id + '" value="0" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>';
            });
            html += '<td style="background: #f0f0f0" class="ciu-cell">0.00 (0.00)</td></tr>';
            $('#assign-table tbody').append(html);
            bindNctKeys();
        }
    }
</script>

<div id="special-task-info" style="display:none" title="Task info" class="buttons">
    <div class="w">
        <input type="hidden" id="nct-id" class="text" />
        <input type="hidden" id="nct-phase-id" class="text" />
        <div class="wd-input">
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'Task') ?></label>
            <input type="text" id="nct-name" class="text" />
        </div>
        <?php $priority = json_decode($listPrioritiesJson, true);?>
        <div class="wd-input">
            <!-- <label><?php echo __('Priority', true);?></label> -->
            <select id="nct-priority">
                <option value=""><?php __('Priority') ?></option>
                <?php
                    foreach($priority['Priorities'] as $p){
                        echo '<option value="' . $p['ProjectPriority']['id'] . '">' . $p['ProjectPriority']['priority'] . '</option>';
                    }
                ?>
            </select>
       </div>
       <div class="wd-input">
            <!-- <label><?php echo __('Status', true);?></label> -->
            <select id="nct-status">
                <option value=""><?php __('Status') ?></option>
                <?php
                    foreach($priority['Statuses'] as $p){
                        echo '<option value="' . $p['ProjectStatus']['id'] . '">' . $p['ProjectStatus']['name'] . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="wd-input" <?php echo !isset($projectName['Project']['id']) ? 'style="display: none"' : '' ?>>
            <select id="nct-profile">
                <option value="" style="font-weight: bold"><?php __('Profile') ?></option>
                <?php
                    foreach($priority['Profiles'] as $p){
                        echo '<option value="' . $p['Profile']['id'] . '">' . $p['Profile']['name'] . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="wd-input">
            <select id="nct-range-type">
                <option value="0"><?php __('Day') ?></option>
                <option value="1"><?php __('Week') ?></option>
                <option value="2"><?php __('Month') ?></option>
            </select>
        </div>
        <hr>
        <div class="wd-input" style="clear: both;">
            <label style="margin-top: 5px;margin-right: 5px"><?php __d(sprintf($_domain, 'Project_Task'), 'Assigned To') ?></label>
            <select id="nct-resources-list" multiple>
                <option value=""><?php __('- Select -') ?></option>
                <?php
                    foreach($priority['Employees'] as $p){
                        echo '<option value="' . $p['Employee']['id'] . '-' . $p['Employee']['is_profit_center'] . '" id="res-' . $p['Employee']['id'] . '-' . $p['Employee']['is_profit_center'] . '" data-name="' . $p['Employee']['name'] . '">' . $p['Employee']['name'] . '</option>';
                    }
                ?>
            </select>

            <?php if( isset($companyConfigs['manual_consumed']) && $companyConfigs['manual_consumed'] ): ?>
                <label style="width: auto"><?php __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed') ?></label>
                <input type="text" id="nct-manual" class="text" value="0" style="width: 100px" />
            <?php endif ?>
        </div>
        <hr>
        <div class="wd-input">
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'Start date') ?></label>
            <input type="text" id="nct-start-date" class="text" readonly="readonly"  style="width: 120px" />
        </div>
        <div class="wd-input">
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'End date') ?></label>
            <input type="text" id="nct-end-date" class="text" readonly="readonly" style="width: 120px" />
        </div>
        <div class="wd-input">
            <button class="btn btn-ok" id="create-range" style="margin-left: 10px"></button>
        </div>
        <div class="wd-input">
            <label style="width: 100px"><?php __d(sprintf($_domain, 'Project_Task'), 'Workload') ?></label>
            <input type="text" id="nct-total-workload" class="text" readonly style="border: 0; color: #08c; font-weight: bold; width: 70px" />
            <input type="text" id="per-workload" class="text" style="width: 70px" />
            <button class="btn btn-ok" id="fill-workload" style="margin-left: 5px"></button>
        </div>
        <hr>
        <div class="wd-input" style="margin-bottom: 5px; margin-right: 10px">
            <div id="nct-range-picker" class="range-picker" style="display: none; margin-bottom: 5px"></div>
            <a id="add-range" href="javascript:;" class="btn-text btn-green range-picker" style="display: none;">
                <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" />
                <span><?php __('Create') ?></span>
            </a>
            <a id="reset-range" class="btn-text range-picker" href="javascript:;" style="display: none;">
                <img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
                <span><?php __('Reset') ?></span>
            </a>

            <div id="date-list" class="start-end" style="margin-bottom: 5px"></div>
            <a id="add-date" href="javascript:;" class="btn-text btn-green start-end disabled">
                <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" />
                <span><?php __('Create') ?></span>
            </a>
            <a id="nct-reset-date" class="btn-text start-end" href="javascript:;">
                <img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
                <span><?php __('Reset') ?></span>
            </a>
        </div>
        <div id="assign-list" style="float: left; width: 800px; margin-right: 30px; margin-bottom: 5px">
            <table id="assign-table">
                <thead>
                    <tr>
                        <td width="15%" class="bold base-cell null-cell">&nbsp;</td>
                        <td class="base-cell" id="abcxyz"><?php __d(sprintf($_domain, 'Project_Task'), 'Consumed') ?> (<?php __d(sprintf($_domain, 'Project_Task'), 'In Used') ?>)</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="base-cell"><?php __('Total') ?></td>
                        <td class="base-cell" id="total-consumed">0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <hr>
        <ul class="type_buttons">
            <li><a class="cancel" id="cancel-special" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
            <li><a id="save-special" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
            <li id="nct-progress" style="display: none; margin-right: 10px"><?php __('Saving...') ?></li>
        </ul>
    </div>
</div>
<div id="collapse" style="padding:4px; cursor:pointer; background-color:#FFF; color:#F00; display:none; position:absolute; top:0; right:0; z-index:9999999999" onclick="collapseTaskScreen();" >
    <button class="btn btn-esc"></button>
</div>

<script>
function loadHolidays(year){
    if( typeof Holidays[year] == 'undefined' ){
        $.ajax({
            url: '<?php echo $this->Html->url('/') ?>holidays/getYear/' + year,
            dataType: 'json',
            success: function(data){
                Holidays[year] = data;
                applyDisableDays();
            }
        });
    }
}
function applyDisableDays(){
    hlds = [];
    $.each(Holidays, function(year, list){
        hlds = hlds.concat(list);
    });
    $('#date-list').multiDatesPicker('addDates', hlds, 'disabled');
}
function refreshPicker(){
    var start = $('#nct-start-date').datepicker('getDate'),
        end = $('#nct-end-date').datepicker('getDate');
    if( start && end && start <= end ){
        $('#date-list').datepicker('setDate', start);
        if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length > 0 ){
            $('#add-date').removeClass('disabled');
        }
        //new enhancement
        resetRange();
    }
    else {
        $('#add-date').addClass('disabled');
    }
    if( !start )year = new Date().getFullYear();
    else year = start.getFullYear();
    loadHolidays(year);
    resetPicker();
    $('#date-list').datepicker('refresh');
    $('#nct-range-picker').datepicker('refresh');
}
//if date in disabled fields, reject
function isValidDate(d, start, end){
    var date = d.getDay();
    return d >= start && d <= end && Workdays[date] == 1;
}
function getValidDate(){
    var result = [];
    var dates = $('#date-list').multiDatesPicker('getDates', 'object');
    var start = $('#nct-start-date').datepicker('getDate'),
        end = $('#nct-end-date').datepicker('getDate');
    for(var i = 0; i < dates.length; i++){
        if( isValidDate(dates[i], start, end) ){
            var date = dates[i].getDate(),
                month = dates[i].getMonth() + 1;
            result.push( (date < 10 ? '0' + date : date) + '-' + (month < 10 ? '0' + month : month) + '-' + dates[i].getFullYear() );
        }
    }
    return result;
}
var xurl = <?php echo json_encode($this->Html->url('/')) ?> + (project_id ? 'project_tasks/' : 'activity_tasks/');
function openAttachmentDialog(){
    var me = $(this), taskId = me.prop('alt');
    $('#UploadId').val(taskId);
    $('#dialog_attachement_or_url').dialog('open');
}
function openAttachment(){
    var me = $(this), taskId = me.prop('alt');
    window.open(xurl + 'view_attachment/' + taskId, '_blank');
}
function deleteAttachment(){
    var me = $(this), taskId = me.prop('alt');
    if( confirm('<?php __('Delete?') ?>') ){
        var panel = project_id ? Ext.getCmp('pmstreepanel') : Ext.getCmp('pmstreepanelactivity');
        panel.setLoading(i18n('Please wait'));
        //call ajax
        $.ajax({
            url: xurl + 'delete_attachment/' + taskId,
            complete: function(){
                panel.getStore().getById(taskId).set('attachment', null);
                panel.getView().refresh();
                panel.setLoading(false);
            }
        })
    }
}
function syncX(file){
    $.ajax({
        url: xurl + 'syncX',
        type: 'POST',
        data: {data: {file: file}}
    });
}
    $(document).ready(function(){
        $('#date-list').multiDatesPicker({
            dateFormat: 'dd-mm-yy',
            separator: ',',
            //numberOfMonths: [1,3],
            beforeShowDay: function(d){
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( !start || !end || start > end ){
                    return [false, '', ''];
                }
                //var date = d.getDay();
                return [ isValidDate(d, start, end) ,'', ''];
            },
            onChangeMonthYear: function(year){
                loadHolidays(year);
            }
            // onSelect: function(d){
            //     if( getValidDate().length > 0 ){
            //         $('#add-date').removeClass('disabled');
            //     }
            //     else {
            //         $('#add-date').addClass('disabled');
            //     }
            // }
        });
        $('#dialog_attachement_or_url').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 500,
            close: function(){
                $('.update_url').val('');
                $('.update_attach_class').val('');
            }
        });
        var isSaving = false;
        $("#ok_attach").click(function(){
            if( isSaving )return false;
            isSaving = true;
            $('.cancel, .new').addClass('grayscale');
            $('#action-attach-url').css('display', 'none');
            $('.browse').css('display', 'block');
            var form = $("#form_dialog_attachement_or_url");

            var panel = project_id ? Ext.getCmp('pmstreepanel') : Ext.getCmp('pmstreepanelactivity'),
                taskId = $('#UploadId').val(),
                params = {
                    url: form.prop('action'),
                    form: form[0],
                    method: 'POST',
                    success: function(response){
                        var data = Ext.JSON.decode(response.responseText);
                        if( data.status ){
                            //update panel
                            try {
                                panel.getStore().getById(taskId).set('attachment', data.attachment);
                                panel.getView().refresh();
                            }catch(ex){
                            }
                            //sync
                            if( data.file && data.sync ){
                                syncX(data.file);
                            }
                        }
                        $("#dialog_attachement_or_url").dialog('close');
                    },
                    failure: function(response){
                        console.log(response.responseText);
                    },
                    callback: function(){
                        isSaving = false;
                        $('.cancel, .new').removeClass('grayscale');
                    }
                };
            //if use url
            if( $('#gs-url').hasClass('gs-url-add') ){
                var url = $.trim($('.update_url').val());
                if( !url )return false;
            }
            //else upload document
            else {
                var file = $('.update_attach_class').val();
                if( !file )return false;
                params.isUpload = true;
            }
            //make ajax call
            Ext.Ajax.request(params);
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
    });
</script>
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => isset($projectName['Project']['id']) ? 'project_tasks' : 'activity_tasks', 'action' => 'update_document')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachment") ?></label>
                <p id="gs-attach"></p>
                <?php echo $this->Form->hidden('id', array('rel' => 'no-history', 'value' => '')) ?>
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
                    'placeholder' => 'Ex: www.example.com',
                    'rel' => 'no-history'
                ));
                ?>
            </div>
            <p style="margin-left: 20px;font-size: 12px;">
                <span style="color: green;"><?php __('Allowed file type') ?>:</span> <?php echo str_replace(',', ', ', $fileTypes) ?>
            </p>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel" onclick="$('#dialog_attachement_or_url').dialog('close')"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
