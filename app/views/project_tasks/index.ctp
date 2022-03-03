<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->css('preview/upload-popup'); ?>
<?php echo $html->css('preview/component');?>
<?php echo $html->script('dropzone.min');
	  echo $html->css('preview/component'); ?>
<?php
// debug($projectTasks); exit;
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
?>
<div id="loading-mask"></div>
<div id="loading">
  <div class="loading-indicator">
  </div>
</div>
<?php echo $html->css(array('projects','slick_grid/slick.edit','gantt','project_task')); ?>
<script type="text/javascript">
    var showProfile = <?php echo isset($companyConfigs['activate_profile']) ? (string) $companyConfigs['activate_profile'] : '0' ?>;
    var is_manual_consumed = <?php echo isset($companyConfigs['manual_consumed']) ? (string) $companyConfigs['manual_consumed'] : '0' ?>;
    var gap_linked_task = <?php echo isset($companyConfigs['gap_linked_task']) ? (string) $companyConfigs['gap_linked_task'] : '0' ?>;
    var task_no_phase = <?php echo isset($companyConfigs['task_no_phase']) ? (string) $companyConfigs['task_no_phase'] : '0' ?>;
    var create_ntc_task = <?php echo isset($companyConfigs['create_ntc_task']) ? (string) $companyConfigs['create_ntc_task'] : '0' ?>;
    var webroot = <?php echo json_encode($this->Html->url('/')) ?>;
    var hightlightTask = <?php echo json_encode(isset($_GET['id']) ? $_GET['id'] : '') ?>;
    var canModify = <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .ui-dialog{font-size:11px;}.ui-widget{font-family:Arial,sans-serif;}#dialog_import_CSV label{color:#000;}.buttons ul.type_buttons{padding-right:10px!important;}.type_buttons .error-message{background-color:#FFF;clear:both;color:#D52424;display:block;width:212px;padding:5px 0 0;}.form-error{border:1px solid #D52424;color:#D52424;}#dialog_import_MICRO label{color:#000;}
    .phase-order-handler{ width:50px;}
    ul#displayFreeze {display: inline-block; vertical-align: top;}
    #wd-container-main .wd-layout{padding-bottom: 15px !important;}
    #menuitem-1060-itemEl, #menuitem-1061-itemEl, #menuitem-1053-itemEl, #menuitem-1054-itemEl{display: none;}
    .buttons{}
    #ProjectOffFreeze{}
    .check-freeze label{
        margin-left: 5px;
    }
    #GanttChartDIV{width: 100%;}
    .delay-plan{padding: 0px 0 0 0px;font-weight: bold; text-align:left; font-size:15px; color:#5fa1c4;}
    .gantt-wrapper{width: 100%;}
    #table-in-ex tr th, tr td.left-column{
        background: url('<?php echo $this->Html->url('/img/front/bg-head-table.png');?>') repeat-x #06427A;
        border-right: 1px solid #5fa1c4;
        color: #fff;
        height: 28px;
        vertical-align: middle;
        padding-left: 3px;
    }
    #table-in-ex {width: 640px;float: left;}
    #table-in-ex tr td.left-column{text-align: left; width:140px;}
    #table-in-ex tr td{
        border: 1px solid silver;
        height: 28px;
        text-align: right;
    }
    #table-in-ex tr th{
        text-align:center;
    }
    .tbHead td{
        text-align:center;
    }
    #gantt-display{float: left; margin-bottom:10px;margin-top:10px;}
    #displayWorkload tr td{padding-top: 5px;padding-right: 3px;}
    .gantt-msi{
        position: absolute;
    }
    .gantt-msi i{
        background: url("/img/mi.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi-blue i{
        background: url("/img/mi-blue.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .ok-new-project, .cancel-new-project{
        width: 90px;
        border: none;
        height: 27px;
        border-radius: 5px;
    }
    .ok-new-project:hover, .cancel-new-project:hover{
        cursor: pointer;
    }
    .x-column-header-text-wrapper:hover{
        cursor: pointer;
    }
    #btnNoAL, #btnNoAL1, #btnNoAL2{
        margin-left: 190px;
    }
    #modal_dialog_alert{
        min-height: 60px !important;
    }
    #modal_dialog_alert1{
        min-height: 60px !important;
    }
    #modal_dialog_alert2{
        min-height: 60px !important;
    }
    #modal_dialog_confirm{
        min-height: 40px !important;
        margin-left: 50px;
    }
    .gantt-msi-green i{
        background: url("/img/mi-green.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi-orange i{
        background: url("/img/mi-orange.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi span, .gantt-msi-blue span, .gantt-msi-green span, .gantt-msi-orange span{
        float: left;
        white-space: nowrap;
    }
    #mcs_container .gantt-line{
        display:none;
    }
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
        width: auto;
        margin-left: 25px;
    }
    #assign-table td {
        border: 1px solid #bbb;
        padding: 5px;
    }
    #assign-table thead td,
    #assign-table tfoot td {
        background: rgb(100, 163, 199);
        color: #fff;
        font-weight: bold;
        text-align: right;
        border-color: rgb(187, 187, 187);
    }
    #assign-table thead td {
        vertical-align: middle;
        text-align: center;
    }
    #assign-table tbody tr td:not(:first-child) {
        text-align: right;
    }
    #assign-table tbody td input {
        /*width: 98% !important;*/
    }
    #assign-table tbody td input:focus {
        color: #08c;
    }

    #assign-table td.nct-date,
    #assign-table td.bold {
        border-color: rgb(187, 187, 187);
    }
    #assign-table td.nct-date {
        position: relative;
        width: 100px;
        min-width: 100px;
    }
    #assign-table td.nct-date .cancel {
        background: url(/img/reject-small.png) center center no-repeat;
        width: 20px;
        height: 20px;
        padding: 0 !important;
        border-radius: 4px;
        display: inline-block;
        position: absolute;
        /*right: -1140px;*/
        top: 2px;
        text-decoration: none;
        cursor: pointer;
        left: -25px;
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
    .cmd {
        display: inline-block;
        background: #fff url(<?php echo $this->Html->url('/') ?>img/delete.gif) no-repeat center;
        width: 16px;
        height: 16px;
        text-indent: 9999px;
        cursor: pointer;
        border-radius: 3px;
    }
    .cmd-remove-col {
        margin-left: 5px;
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
        border-top: 3px solid rgb(100, 163, 199);
        margin: 10px -10px !important;
    }
    .multiSelect, .multiSelectOptions {
        width: 585px !important;
    }
    .multiSelect span {
        width: 285px !important;
        word-wrap: break-word;
    }
    .multiSelect {
        border-color: #bbb !important;
        padding: 1px !important;
        height: auto !important;
        margin-bottom: 5px !important;
    }
    .multiSelectOptions label {
        clear: both;
    }
    .multiSelectOptions label input {
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
    .hide-on-mobile {
        <?php if( $isMobile ): ?>display: none !important;<?php endif ?>
    }
    #nct-range-type:disabled {
        background: #eee;
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
    .milestone-mi{
        background-image: url("/img/mi.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
    }
    .milestone-green{
        background-image: url("/img/mi-green.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
    }
    .milestone-blue{
        background-image: url("/img/mi-blue.png");
        background-repeat: no-repeat;
        display: block;
        padding-left: 20px;
        cursor: pointer;
    }
    .milestone-orange{
        background-image: url("/img/mi-orange.png");
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
    .div-skip label{
        margin-left: 10px;
        text-align: left;
    }
    .div-skip input{
        width: 240px;
    }
    .wd-add-project span {
        background: url(../img/ui/btn-blue-right.png) right top no-repeat;
        vertical-align: top;
        cursor: pointer;
        height: 32px;
        border: 0;
        outline: 0;
        margin: 0;
        padding: 0;
        opacity: 1;
        overflow: hidden;
        position: relative;
        display: inline-block;
        text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
        line-height: 32px;
        font-weight: bold;
        color: #fff;
        padding-right: 10px;
    }
    .wd-add-project {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: #5e9ec3;
        background-image: url(/img/ui/blank-plus.png);
        background-origin: padding-box;
        background-position: left top;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        float: left;
        height: 32px;
        width: 32px;
        line-height: 33px;
        padding-left: 27px;
        text-decoration: none;
        border-radius: 3px;
    }
    .content{
        border-style:none solid;
        clear: both;
        min-height: 60px;
        border-left: none;
    }
    .avartar-text,
    .avartar-image{
        display: inline-block;
        vertical-align: top;
        margin-top: 2px;
    }
    #content-comment-id{
        overflow-y: auto;
        padding-top: 20px;
    }
    .avartar-image-img{
        width: 35px;
        height: 35px;
        padding: 5px;
        border: 1px solid #bbb;
        border-radius: 3px;
    }
	.avartar-text .avartar-image-img{
		display: block;
		line-height: 35px;
		text-align: center;
		color: #ffffff;
		font-weight: 600;
	}
	.avartar-text .avartar-image-img span{
		background: #56aaff;
		display: block;
		font-size: 14px;
        width: 35px;
        height: 35px;
		text-transform: uppercase;
	}
    .content-comment{
        display: inline-block;
        width: calc( 100% - 65px);

    }
    .comment{
        width: 98%;
        height: 16px;
        background: #fff;
        border: 1px solid transparent;
        transition: border-color 0.5s;
    }
    .name h5{
        float: left;
        color: #08c;
        font-size: 14px;
        display: inline-block;
    }
    .name em{
        font-size: 12px;
        color: #888;
        display: inline-block;
        margin-left: 10px;
    }
    .text-textarea{
        width: calc( 100% - 55px);
        height: 44px;
        border: 1px solid #0cb0e0;
        color: #242424;
        font: normal normal 1em Arial, Helvetica, sans-serif;
        outline: 0;
        border-left: none;
        display: inline-block;
    }
    #wd-container-footer{
        display: none;
    }
    .gantt-chart-wrapper{
        border: 1px solid #0cb0e0;
    }
    .gantt{
        margin-left: 0;
    }
    .gantt-node table div{
        text-align: center;
    }
    body {
        overflow: hidden;
    }
    .setting_date{
        background: url(/img/ui/icon-settings.png) no-repeat !important;
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: top;
        opacity: 1;
    }
    #content-comment-id{
         height: 465px;
         overflow-y: scroll;
         border-bottom: 1px solid #ccc;
    }
    .comment_form{
        width: 500px !important;
        height: 585px !important;
        border-color: #fff;
    }
    .comment_form .x-window-header-default-top .x-window-header-title-default{
        color: #000;
    }
    .comment_form .x-window-header-default-top{
        background-color: #fff;
        color: #000;
        border: 1px solid #ccc;
        border-width: 1px !important;
        padding: 12px;
    }
    body .comment_form{
        border: none;
    }
    .submit-btn-msg{
        width: 50px;
        height: 50px;
        background-color: #56aaff !important;
        display: inline-block;
        vertical-align: top;
        border: none;
        cursor: pointer;
    }
    .redirect-kanban{
        text-align: center;
        line-height: 34px;
        font-size: 17px;
    }
	#content_comment .append-comment{
		max-width: 270px;
	}
	.x-grid-cell.x-grid-dirty-cell{
		background: none;
	}
	#special-task-info .wd-input .option-content  label{
		display: block;
		text-align: left;
		width: inherit;
	}
</style>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_tasks', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<!-- dialog_import -->
<div id="dialog_import_CSV" style="display:none" title="<?php __('Import CSV file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_csv', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- dialog_import -->
<div id="dialog_import_MICRO" style="display:none" title="<?php __('Import MICRO file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadFormMicro', 'type' => 'file',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_task_micro_project', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[micro_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.xml)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-micro-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error-micro"></li>
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
                <div class="wd-title" style="margin-bottom:0 !important; float:left;">
                    <div id="title-1" style="clear: both; float: left; margin-right: 5px">
                        <h2 class="wd-t1" style="line-height: 1.25"><?php echo $projectName['Project']['project_name'] ?></h2>
                        <!-- <a href="javascript:;" id="clean-filters" title="<?php __('Reset filter') ?>" class="btn btn-reset"></a> -->
                            
                        <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) {?>
                        <a href="<?php echo $html->url('/kanban/index/'.$projectName['Project']['id']) ?>" class="btn redirect-kanban" style="margin-right:5px;" title="<?php __('Kanban') ?>"><i class="icon-grid"></i></a>
                        <?php } ?>
                        <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="clean-filters" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                        <a href="javascript:;" onclick="expandTaskScreen();" class="btn btn-fullscreen hide-on-mobile"></a>
                        <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"></a>
                        <a href="javascript:void(0);" class="btn btn-skip hide-on-mobile" id="skip_value" title="<?php __('Skip') ?>"><span><?php __('Skip') ?></span></a>
                        <a href="<?php echo $html->url("/project_tasks/exportExcel/" . $projectName['Project']['id']) ?>" class="export-excel-icon-all hide-on-mobile" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
						<?php 
						$is_show_import_csv = isset($companyConfigs['import_task_csv']) ? $companyConfigs['import_task_csv'] : 1;
						$is_show_import_xml = isset($companyConfigs['import_task_xml']) ? $companyConfigs['import_task_xml'] : 1;
						if( $is_show_import_csv == 1) { ?>
                        <a href="javascript:void(0)" class="import-excel-icon-all hide-on-mobile" id="import_CSV" title="<?php __('Import CSV file')?>"><span><?php __('Import CSV') ?></span></a>
						<?php } 
						if( $is_show_import_xml == 1) { ?>
                        <a href="javascript:void(0)" class="import-micro-pro-icon-all hide-on-mobile" id="import_MICRO" title="<?php __('Import Micro project')?>"><span><?php __('Import Micro project') ?></span></a>
						<?php } ?>
                        <?php if( $projectName['Project']['category'] == 2 ): ?>
                            <a id="reset-date" href="<?php echo $html->url('/project_tasks/reset_date/' . $project_id) ?>" title="<?php __('Reset the start and end date of all tasks to match with their phases') ?>" class="btn btn-reset" style= "display: none"></a>
                        <?php endif ?>
                    </div>
                    <div class="filter-by-type">
                        <span class="filter-type type-green" data-type="green"></span>
                        <span class="filter-type type-red" data-type="red"></span>
                    </div>
                    <?php if( $settingP['ProjectSetting']['show_freeze'] ):?>
                    <div class="buttons hide-on-mobile" style="display: inline-block; vertical-align: top">
                        <?php if((($myRole=='pm')||($myRole=='admin'))&&($projectName['Project']['is_freeze']==0)){?>
                            <a href="<?php echo $this->Html->url('/project_tasks/freeze/'.$project_id);?>" id="submit-freeze-all-top" class="validate-for-validate validate-for-validate-top" title="<?php __('Freeze')?>"><span><?php __('Freeze'); ?></span></a>
                        <?php }?>
                        <?php if(($myRole=='admin')&&($projectName['Project']['is_freeze']==1)){?>
                            <a href="<?php echo $this->Html->url('/project_tasks/unfreeze/'.$project_id);?>" id="submit-unfreeze-all-top" class="validate-for-reject validate-for-reject-top" title="<?php __('Unfreeze')?>"><span><?php __('Unfreeze'); ?></span></a>
                         <?php }?>
                        <ul id="displayFreeze">
                            <?php
                            if(!empty($modifyF['Employee']['fullname']) && !empty($projectName['Project']['freeze_time']) ){
                                if( $projectName['Project']['is_freeze'] ==1 ){ ?>
                                    <li class="unfreeze-freeze"><?php echo sprintf(__('Freezed by %s at %s', true), $modifyF['Employee']['fullname'], date('d/m/Y h:i:s',$projectName['Project']['freeze_time']));?>  </li>
                                <?php } else { ?>
                                    <li class="unfreeze-freeze"><?php echo sprintf(__('Unfreezed by %s at %s', true), $modifyF['Employee']['fullname'], date('d/m/Y h:i:s',$projectName['Project']['freeze_time']));?>  </li>
                                <?php }
                            }
                            ?>
                            <li>
                                <?php
                                echo $this->Form->input('Project.off_freeze', array(
                                    'rel' => 'no-history',
                                    'label' => false,
                                    'checked'=>$projectName['Project']['off_freeze']?true:false,
                                    'div' => false,
                                    'type' => 'checkbox', 'legend' => false, 'fieldset' => false
                                ));
                                ?>
                                <label for="ProjectOffFreeze"><?php echo __('Display initial information',true);?></label>
                            </li>
                        </ul>
                    </div>
                    <?php endif;?>
                    <div id="gantt-display">
                    <table id="table-in-ex" class="hide-on-mobile" <?php echo isset($companyConfigs['display_synthesis']) && $companyConfigs['display_synthesis'] == '0' ?'style="display:none"':''?>>
                        <thead>
                            <tr>
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
                                                            'thousands' => ''
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
                                        <?php echo $this->Number->format(round($consumedInter, 2), array(
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
                                                        'thousands' => ''
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
                                                'thousands' => ''
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
                                            echo $this->Number->format(round($consumedExter['ProjectTask']['consumedex'],2), array(
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
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div id="message-place" style="clear: both;">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <!-- Gantt -->
                <div id="GanttChartDIV">
                    <div class="delay-plan" style="clear:both;">
                        <?php echo __('Delay:');?>
                        <?php echo $delay;?>
                        <?php echo __('M.D');?>
                    </div>
                    <?php
                    $rows = 0;
                    $start = $end = 0;
                    $data = $projectId = $conditions = array();
                    $stones = array();
                    $_milestoneColor = array();
                    if (!empty($projectMilestones)) {
                        foreach ($projectMilestones as $p) {
                            $_start = strtotime($p['milestone_date']);
                            if (!$start || $_start < $start) {
                                $start = $_start;
                            } elseif (!$end || $_start > $end) {
                                $end = $_start;
                            }
                            $stones[] = array($_start, $p['project_milestone'], $p['validated']);
                            // tinh mau cho milestone.
                            if(!empty($p['validated'])){
                                $_milestoneColor[$p['id']] = 'milestone-green';
                            } else {
                                $currentDate = strtotime(date('d-m-Y', time()));
                                $k = strtotime($p['milestone_date']);
                                if ($currentDate > $k) {
                                    $_milestoneColor[$p['id']] = 'milestone-mi';
                                } elseif ($currentDate < $k) {
                                    $_milestoneColor[$p['id']] = 'milestone-blue';
                                } else {
                                    $_milestoneColor[$p['id']] = 'milestone-orange';
                                }
                            }
                        }
                    }
                    foreach ($projects as $project) {
                        $_data = array(
                            'name' => $project['Project']['project_name'],
                            'phase' => array(),
                        );
                        $projectId[$project['Project']['id']] = $project['Project']['project_name'];
                        if (!empty($project['ProjectPhasePlan'])) {
                            foreach ($project['ProjectPhasePlan'] as $phace) {
                                $_phase = array(
                                    'name' => !empty($phace['ProjectPhase']['name']) ? $phace['ProjectPhase']['name'] : '',
                                    'start' => $this->Gantt->toTime($phace['phase_planed_start_date']),
                                    'end' => $this->Gantt->toTime($phace['phase_planed_end_date']),
                                    'rstart' => $this->Gantt->toTime($phace['phase_real_start_date']),
                                    'rend' => $this->Gantt->toTime($phace['phase_real_end_date']),
                                    'color' => !empty($phace['ProjectPhase']['color']) ? $phace['ProjectPhase']['color'] : '#004380'
                                );
                                if ($_phase['rstart'] > 0) {
                                    $_start = min($_phase['start'], $_phase['rstart']);
                                } else {
                                    $_start = $_phase['start'];
                                }
                                if (!$start || ($_start > 0 && $_start < $start)) {
                                    $start = $_start;
                                }
                                $_end = max($_phase['end'], $_phase['rend']);
                                if (!$end || $_end > $end) {
                                    $end = $_end;
                                }
                                $_data['phase'][] = $_phase;
                            }
                        }
                        $data[] = $_data;
                    }
                    unset($projects, $project, $_data, $_phase, $phase);
                    $summary = isset($this->params['url']['summary']) ? (bool) $this->params['url']['summary'] : false;
                    $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
                    if (empty($start) || empty($end)) {
                        //echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                    } else {
                        $this->Gantt->create($type, $start, $end, $stones, false , false);

                        foreach ($data as $value) {
                            $rows++;
                            if (empty($value['phase'])) {
                                $this->Gantt->drawLine(__('no data exit', true), 0, 0, 0, 0, '#ffffff', true);
                            } else {
                                foreach ($value['phase'] as $node) {
                                    $color = '#004380';
                                    if (!empty($node['color'])) {
                                        $color = $node['color'];
                                    }
                                    $this->Gantt->drawLine($node['name'], $node['start'], $node['end'], $node['rstart'], $node['rend'], $color, true);
                                }
                            }
                            $this->Gantt->drawEnd($value['name'], false);
                        }
                        $this->Gantt->end();
                    }
                    ?>
                </div>
                <!-- Gantt.end -->
                <div class="wd-table" id="project_container" style="">
                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden; " class="slick-pager">
                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php
$i18n = array(
    //for extjs
    'Read only' => __('Read only', true),
    'New Task' => __('New Task', true),
    'Update' => __('Update', true),
    'Resources' => __('Resources', true),
    'Filter' => __('Filter', true),
    'Save' => __('Save', true),
    'Saved' => __('Saved', true),
    'Not saved' => __('Not saved', true),
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
    'Move Here' => __('Move Here', true),
    'Move part and phase in the screens part and phase' => __('Move part and phase in the screens part and phase', true),
    'Workload {1} M.D , Workload filled {0} M.D.' => __('Workload {1} M.D , Workload filled {0} M.D.', true),
    'Please wait' => __('Please wait', true),
    'The task name already exists' => __('The task name already exists', true),
    'No consumed and the current date > start date' => __('No consumed and the current date > start date', true),
    'Consumed > Workload' =>  __('Consumed > Workload', true),
    'Task not closed and the current date > end date' => __('Task not closed and the current date > end date', true),
    '{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?' => __('{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?', true),
    'This task is in used/ has consumed' => __('This task is in used/ has consumed', true),
    'Reset' => __('Reset', true),
    'Task name already existed' => __('Task name already existed', true),
    'Edit' => __('Edit', true),
    'Keep the duration of the task ' => __('Keep the duration of the task ', true),
    'Check all' => __('Check all', true),
    'Uncheck all' => __('Uncheck all', true),
);
$i18n = array_merge($i18n, $this->requestAction('/translations/getByLang/Project_Task'));
$i18n = json_encode($i18n);
?>
<script>
    var i18n_text = <?php echo $i18n?>;
    //var columns_order = <?php echo json_encode(array_merge(array('id|1'), $this->requestAction('/admin_task/getTaskSettings'))) ?>;
    var columns_order = <?php echo json_encode($this->requestAction('/admin_task/getTaskSettings')) ?>;
    var hide_unit = hide_milestone = false;
    var _milestoneColor = <?php echo json_encode($_milestoneColor) ?>;
    $.each(columns_order, function(index, value){
        var _v = value.split('|');
        if(_v[0] == 'UnitPrice'){
            if(_v[1] == 1) hide_unit = true;
        }
        if(_v[0] == 'Milestone'){
            if(_v[1] == 1) hide_milestone = true;
        }
    });
    var manual
    function i18n(text){
        if( typeof i18n_text[text] != 'undefined' )return i18n_text[text];
        return text;
    }
    $(function(){
        $('.gantt-line-n').show();
        $('.gantt-line-s').show();
    });

</script>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="showdetail">
    <div id="gs-popup-header">
        <div class="gs-header-title">
            <ul>
                <li><a href="javascript:;" id="filter_date"><?php echo __("Day", true)?></a></li>
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
<!-- dialog_skip_value -->
<div id="dialog_skip_value" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Skip'); ?>
        <div style="height:auto; overflow: hidden" class="wd-scroll-form">
            <div class="wd-input div-skip" id="wd-input-day-skip">
                <label style="width: 162px; display: none" for="url"><?php echo __("Value by day", true) ?></label>
                <?php
                echo $this->Form->input('value_day', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history',
                    'div' => false,
                    'value' => 0,
                    'style' => 'display: none'
                ));
                ?>
            </div>
            <div class="wd-input" id="wd-input-date-skip">
                <div style="clear: both">
                    <label style="width: 162px; color: red; text-align: left; margin-left: 10px" for="url"><?php echo __("Actual start date", true) ?></label>
                    <p id ='skip-start-date' style="width: 120px; margin-left: 140px; padding-top: 6px"></p>
                </div>
                <div style="clear: both">
                    <label style="width: 162px; color: red; text-align: left; margin-left: 10px" for="url"><?php echo __("New start date", true) ?></label>
                    <input type="text" id="new-start-date-skip" class="text" style="width: 80px" />
                </div>
            </div>
            <div class="wd-input div-skip" id="wd-input-week-skip">
                <label style="width: 162px;" for="url"><?php echo __("Value by week", true) ?></label>
                <?php
                echo $this->Form->input('value_week', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history',
                    'div' => false,
                    'value' => 0,
                    'style' => 'width: 40%'
                ));
                ?>
            </div>
            <div class="wd-input div-skip" id="wd-input-month-skip">
                <label style="width: 162px;" for="url"><?php echo __("Value by month", true) ?></label>
                <?php
                echo $this->Form->input('value_month', array('type' => 'text',
                    'label' => false,
                    'maxlength' => 3,
                    'rel' => 'no-history',
                    'div' => false,
                    'value' => 0,
                    'style' => 'width: 40%'
                ));
                ?>
            </div>
            <ul class="type_buttons" style="">
                <li><img style="margin-left: -382px" src="/img/time-reset-1.png"></li>
                <li><input id="input_time_reset" style="margin-left: -330px;margin-top: 12px;" type="checkbox" name="reset_time" <?php if($projectName['Project']['category'] == 2){ echo 'checked'; }?>></li>
                <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
                <li><a href="javascript:void(0)" class="new" id="ok_skip"><?php __('OK') ?></a></li>
            </ul>
        </div>
        <p id="iz-error" style="display: none; font-size: 12px; color: red; text-align: center"></p>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
</div>
<input type="hidden" value="<?php echo Configure::read('Config.language'); ?>" id="language" />
<input type="hidden" value="<?php echo $this->Session->read('Auth.employee_info.Role.name'); ?>" id="pm_acl" />
<input type="hidden" value="<?php echo $projectName['Project']['is_freeze']; ?>" id="show_freeze" />
<input type="hidden" value="<?php echo $settingP['ProjectSetting']['show_freeze']; ?>" id="is_show_freeze" />
<input type="hidden" value="<?php echo $projectName['Project']['off_freeze']; ?>" id="off_freeze" />
<textarea style="display:none" id="priorityJson"><?php echo $listPrioritiesJson; ?></textarea>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="">
    <div class="dialog-request-message">
    </div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<div id="gs_loader" style="display: none;">
    <div class="gs_loader">
        <p>Please wait, Skip value...</p>
    </div>
</div>
<div id='modal_dialog_confirm' style="display: none">
    <div class='title'>
    </div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnYes' />
    <input class="cancel-new-project" type='button' value='<?php echo __('Cancel', true) ?>' id='btnNo' />
</div>
<div id='modal_dialog_alert' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Select at least a resource or a team', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnNoAL' />
</div>
<div id='modal_dialog_alert1' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Please enter task name', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnNoAL1' />
</div>
<div id='modal_dialog_alert2' style="display: none">
    <div class='title'  style="color: orange;font-size: 14px;text-align: center; margin-bottom: 10px"><?php echo __('Select a start date, end date', true) ?></div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnNoAL2' />
</div>
<div id="template_upload" class="template_upload" style="height: auto; width: 320px;">
	<div class="heading">
        <h4><?php echo __('File upload(s)', true)?></h4>
        <span class="close close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div id="content_comment">
        <div class="append-comment"></div>
    </div> 
    <div class="wd-popup">
        <?php 
        echo $this->Form->create('Upload', array(
            'type' => 'POST',
            'url' => array('controller' => 'kanban','action' => 'update_document', $projectName['Project']['id'])));
            ?>
            <div class="trigger-upload"><div id="upload-popup" method="post" action="/kanban/update_document/<?php echo $projectName['Project']['id']; ?>" class="dropzone" value="" >

            </div></div>
            <?php echo $this->Form->input('url', array(
                'class' => 'not_save_history',
                'label' => array(
                    'class' => 'label-has-sub',
                    'text' =>__('URL Link',true),
                    'data-text' => __('(optionnel)', true),
                    ),
                'type' => 'text',
                'id' => 'newDocURL',  
                'placeholder' => __('https://', true)));    
            ?>                    
            <input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
            <input type="hidden" name="data[Upload][controller]" rel="no-history" value="project_tasks">
        <?php echo $this->Form->end(); ?>
    </div>
    <ul class="actions" style="">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Upload Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Upload Validate') ?></a></li>
    </ul>
</div>

<div id="comment_popup" class="buttons" style="display: none; width: 500px; height: 554px;">
    <div class="heading">
        <span class="close"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div class="task-name"></div>
    <div id="content-comment">
        <div class="append-comment"></div>
    </div> 
</div>
<div class="light-popup"></div>
<?php echo $this->element('dialog_detail_value') ?>
<?php
$listAvartar = array();
$listIdEm = array_keys($listEmployee);
foreach ($listIdEm as $_id) {
    $link = $this->UserFile->avatar($_id, "small");
    $listAvartar[$_id] = $link;
}
?>
<script type="text/javascript">
    // Milestones
    var bandwidth = $('.gantt .gantt-ms .gantt-line').width(), isFull = false;
    var stack =  [],height = 16,icon = 16;
    var listEmployee = <?php echo json_encode($listEmployee) ?>;
	var flag_new_task_id = '';
    var listAvartar = <?php echo json_encode($listAvartar) ?>;
    var budget_settings = <?php echo json_encode(!empty($budget_settings) ? $budget_settings : '&euro;') ?>;
    var $employee_id = <?php echo json_encode($employee_id) ?>;
    var $isEmployeeManager = <?php echo json_encode($isEmployeeManager) ?>;
    var listTaskName = <?php echo json_encode($listTaskName) ?>;
    $('.gantt-line .gantt-msi').each(function(){
        var $element = $(this);
        var $span = $element.find('span');
        var left = $element.position().left;
        var width = $span.width();
        var row = 0;
        if(left+width+icon >= bandwidth ){
            left -= (width + icon) * 2;
            $span.css('marginLeft' , - (width + icon ));
        }
        $(stack).each(function(k,v){
            if(left >= v){
                return false;
            }
            row++;
        });
        stack[row] = left + width + icon;
        $element.css('top' , row * height);
    });
    $('.gantt-ms .gantt-line').height(stack.length * height );
    //END
    $('#update-status').hide();
    $("#btnClose").click(function(){
        $("#showdetail").hide();
    });
    $('.close').on( 'click', function (e) {
        // e.preventDefault();
        $("#template_upload").removeClass('show');
        $("#comment_popup").removeClass('show');
        $(".light-popup").removeClass('show');
    });
    var wdTable = $('.wd-table');
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 400) ? 400 : heightTable;
    wdTable.css({
        height: heightTable,
    });
    var xdelay;
    $(window).resize(function(){
        clearTimeout(xdelay);
        xdelay = setTimeout(function(){
            var treepanel = Ext.getCmp('pmstreepanel');
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
    //clearInterval(flag);
    var flag = setInterval(function(){
        if($('#pmstreepanel-body').find('div table').height()>0){
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
            var treepanel = Ext.getCmp("pmstreepanel");
            treepanel.setHeight(Ext.getBody().getViewSize().height);
            // treepanel.onAfterLoad(function(){
            //     $('#loading').remove();
            // });
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
                treepanel.addTypeFilter(which, true);
                cleanFilter();
            });
            clearInterval(flag);
        }
    }, 1000);
    $('#dialog_import_CSV').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 360,
        height      : 150
    });
    $('#dialog_import_MICRO').dialog({
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
    $("#import_MICRO").click(function(){
        $('.wd-input').show();
        $('#loading').hide();
        $("input[name='FileField[micro_file_attachment]']").val("");
        $(".error-message").remove();
        $("input[name='FileField[micro_file_attachment]']").removeClass("form-error");
        $(".type_buttons").show();
        $('#dialog_import_MICRO').dialog("open");
    });
    $("#import-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[csv_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[csv_file_attachment]']").val()) {
            var filename = $("input[name='FileField[csv_file_attachment]']").val();
            var valid_extensions = /(\.csv)$/i;
            if(valid_extensions.test(filename)) {
                $('#uploadForm').submit();
            } else {
                $("input[name='FileField[csv_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error');
            }
            $("#dialog_import_CSV").dialog("close");
        } else {
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error');
        }
    });
    $("#import-micro-submit").click(function(){
        $(".error-message").remove();
        $("input[name='FileField[micro_file_attachment]']").removeClass("form-error");
        if($("input[name='FileField[micro_file_attachment]']").val()) {
            var filename = $("input[name='FileField[micro_file_attachment]']").val();
            var valid_extensions = /(\.xml)$/i;
            if(valid_extensions.test(filename)) {
                $('#uploadFormMicro').submit();
            } else {
                $("input[name='FileField[micro_file_attachment]']").addClass("form-error");
                jQuery('<div>', {
                    'class': 'error-message',
                    text: 'Incorrect type file'
                }).appendTo('#error-micro');
            }
            $("#dialog_import_MICRO").dialog("close");
        } else {
            jQuery('<div>', {
                'class': 'error-message',
                text: 'Please choose a file!'
            }).appendTo('#error-micro');
        }
    });
    /* table .end */
    var createDialog = function(){
        $('#dialog_skip_value').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 400,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
    }
    $("#skip_value").live('click',function(){
        var _id = $('.x-grid-item-selected').find('span:first').attr('id');
        if(_id !== undefined){
            _id = _id.replace('x-task-', '');
            $.ajax({
                url: '/project_tasks/getClassifyTask/' + _id,
                dataType: 'json',
                data: {
                    type : 'task'
                },
                type: 'POST',
                success: function(result) {
                     value_day = (result.start_date).split("-");
                    $('#skip-start-date').empty().append(value_day[2] +'-'+ value_day[1] +'-'+ value_day[0]);

                    if(result.day <= 0){
                        $('#wd-input-day-skip').css('display', 'none');
                        $('#wd-input-date-skip').css('display', 'none');
                    } else {
                        $('#wd-input-day-skip').css('display', '');
                        $('#wd-input-date-skip').css('display', '');
                    }
                    if(result.week <= 0){
                        $('#wd-input-week-skip').css('display', 'none');
                    } else {
                        $('#wd-input-week-skip').css('display', '');
                    }
                    if(result.month <= 0){
                        $('#wd-input-month-skip').css('display', 'none');
                    } else {
                        $('#wd-input-month-skip').css('display', '');
                    }
                    createDialog();
                    $("#dialog_skip_value").dialog('option',{title:''}).dialog('open');
                }
           });
        } else {
            $.ajax({
                url: '/project_tasks/getClassifyTask/' + <?php echo json_encode($project_id) ?>,
                dataType: 'json',
                data: {
                    type : 'project'
                },
                type: 'POST',
                success: function(result) {
                    value_day = (result.start_date).split("-");
                    $('#skip-start-date').empty().append(value_day[2] +'-'+ value_day[1] +'-'+ value_day[0]);
                    if(result.day <= 0){
                        $('#wd-input-day-skip').css('display', 'none');
                        $('#wd-input-date-skip').css('display', 'none');
                    } else {
                        $('#wd-input-day-skip').css('display', '');
                        $('#wd-input-date-skip').css('display', '');
                    }
                    if(result.week <= 0){
                        $('#wd-input-week-skip').css('display', 'none');
                    } else {
                        $('#wd-input-week-skip').css('display', '');
                    }
                    if(result.month <= 0){
                        $('#wd-input-month-skip').css('display', 'none');
                    } else {
                        $('#wd-input-month-skip').css('display', '');
                    }
                    createDialog();
                    $("#dialog_skip_value").dialog('option',{title:''}).dialog('open');
                }
           });
        }
    });
    $(".cancel").live('click',function(){
        $("#dialog_skip_value").dialog('close');
        $("#dialog_data_CSV").dialog("close");
        $("#dialog_import_CSV").dialog("close");
        $("#dialog_import_MICRO").dialog("close");
    });
    // Chi cho phep nhap so
    $("#SkipValueDay, #SkipValueWeek, #SkipValueMonth").keypress(function (e) {
        if (e.which != 45 && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#iz-error").html("Only input number.").show().fadeOut("slow");
            return false;
        }
    });
    $('#new-start-date-skip').datepicker({
        dateFormat: 'dd-mm-yy',
        beforeShowDay: function(d){
            var date = d.getDay();
            return [Workdays[date] == 1, '', ''];
        },
        onSelect: function(dateText, inst){
            var _d = $('#new-start-date-skip').val();
            var _start_date = <?php echo json_encode($projectName['Project']['start_date']) ?>;
            _d = _d.split('-');
            var d1 = new Date(_d[2] + '-' + _d[1] + '-' + _d[0]);
            var d2 = new Date(_start_date);
            if(d1 <= d2){
                var calculateDate = 0;
                d1 = d1.getTime();
                d2 = d2.getTime();
                var _d1 = d2;
                while(_d1 > d1){
                    var d1Date = new Date(_d1);
                    d1Date = d1Date.getDay();
                    if(Workdays[d1Date] == 1) calculateDate--;
                    _d1 = _d1 - (1000*60*60*24);
                }
                $('#SkipValueDay').val(calculateDate);
            } else {
                var calculateDate = 0;
                d1 = d1.getTime();
                d2 = d2.getTime();
                var _d1 = d1;
                while(_d1 > d2){
                    var d1Date = new Date(_d1);
                    d1Date = d1Date.getDay();
                    if(Workdays[d1Date] == 1) calculateDate++;
                    _d1 = _d1 - (1000*60*60*24);
                }
                $('#SkipValueDay').val(calculateDate);
            }
        }
    });
    $("#ok_skip").click(function(){
        // tinh valDay
        var _d = $('#new-start-date-skip').val();
        var get_date = $('#skip-start-date').html();
        get_date = get_date.split('-');
        var get_date = new Date(get_date[2] + '-' + get_date[1] + '-' + get_date[0]);
       // var _start_date = <?php echo json_encode($projectName['Project']['start_date']) ?>;
        var _start_date = get_date;
        _d = _d.split('-');
        var d1 = new Date(_d[2] + '-' + _d[1] + '-' + _d[0]);
        var d2 = new Date(_start_date);
        var calculateDate = 0;
        if(d1 <= d2){
            d1 = d1.getTime();
            d2 = d2.getTime();
            var _d1 = d2;
            while(_d1 > d1){
                var d1Date = new Date(_d1);
                d1Date = d1Date.getDay();
                if(Workdays[d1Date] == 1) calculateDate--;
                _d1 = _d1 - (1000*60*60*24);
            }
            $('#SkipValueDay').val(calculateDate);
        } else {
            d1 = d1.getTime();
            d2 = d2.getTime();
            var _d1 = d1;
            while(_d1 > d2){
                var d1Date = new Date(_d1);
                d1Date = d1Date.getDay();
                if(Workdays[d1Date] == 1) calculateDate++;
                _d1 = _d1 - (1000*60*60*24);
            }
            $('#SkipValueDay').val(calculateDate);
        }
        var valDay = calculateDate;
        var valWeek = $('#SkipValueWeek').val(),
            valMonth = $('#SkipValueMonth').val();
        var checked = $('#input_time_reset').attr('checked');
        if(checked !== undefined && checked == 'checked'){
            checked = 1;
        } else {
            checked = 0;
        }
        if(valDay == '' && !valDay && valWeek == 0 && valMonth == 0){
            $('#iz-error').html('The value day is not blank!');
            $('#iz-error').show();
        // } else if(valWeek == '' || !valWeek){
        //     $('#iz-error').html('The value week is not blank!');
        //     $('#iz-error').show();
        // } else if(valMonth == '' || !valMonth){
        //     $('#iz-error').html('The value month is not blank!');
        //     $('#iz-error').show();
        } else {
            var modelId = <?php echo json_encode($projectName['Project']['id']);?>;
            $.ajax({
                url: '/project_tasks/skipValue/project/' + modelId + '/' + valDay + '/' + valWeek + '/' + valMonth,
                //async: false,
                type: 'POST',
                data: {
                    checked: checked
                },
                beforeSend: function(){
                    $("#dialog_skip_value").dialog('close');
                    $('#gs_loader').css('display', 'block');
                },
                success: function() {
                    _runStaffing(function(){
                        window.location.reload(0);
                    });
                }
           });
        }
    });
    function _runStaffing(success){
        $.ajax({
            url: '/project_tasks/staffingWhenUpdateTask/<?php echo $projectName['Project']['id'] ?>',
            success: success
        });
    }
</script>
<?php echo $this->Html->script(array(
    'extjs/ext5-include',
    'extjs/app/app',
    'jquery-ui.multidatespicker',
    'jquery.multiSelect',
    'jquery.scrollTo'
)); ?>
<?php echo $this->Html->css(array(
    'extjs/resources/css/ext-custom',
    'jquery.multiSelect',
)); ?>
<!-- export excel  -->
<script type="text/javascript">
    $('#ProjectOffFreeze').click (function(){
      var thisCheck = $(this);
      if(thisCheck.is(':checked')) {
        $.post("<?php echo $this->Html->url('/project_tasks/update_initial/'.$project_id.'/1');?>", function(data){
            location.reload();
        });
      }else{
        $.post("<?php echo $this->Html->url('/project_tasks/update_initial/'.$project_id.'/0');?>", function(data){
            location.reload();
        });
      }
    });
    $('#absence-table-fixed').html('Freeze');
    var openDialog = function(title,callback){
        var $dialog = $('#add-comment-dialog').attr('title' , title);
        $dialog.dialog({
            zIndex : 10000,
            modal : true,
            minHeight : 50,
            close : function(){
                $dialog.dialog('destroy');
            }
        });
        $dialog.find('a.ok').unbind().click(function(){
            if(!$.isFunction(callback)) {
                $dialog.dialog('close');
            } else {
                callback.call(this);
            }
            return false;
        });
        $dialog.find('a.cancel').unbind().click(function(){
            $dialog.dialog('close');
            return false;
        }).toggle($.isFunction(callback));
    };
    var freeze =  $('ul#displayFreeze li.unfreeze-freeze').width();
    var widthF = parseFloat(freeze)+35;
    if ($('ul#displayFreeze li').length > 1) {
        widthF = parseFloat(freeze) + 75;
    }
    $('ul#displayFreeze').css('width',widthF);

    //EXPAND TREE
    $(document).keyup(function(e) {
        if (window.event) {
            var value = window.event.keyCode;
        } else
            var value=e.which;
        if (value == 27) { collapseTaskScreen(); }
    });
    function collapseTaskScreen() {
        $('#collapse').hide();
        $('#project_container').removeClass('treeExpand');
        isFull = false;
        $(window).trigger('resize');
    }
    function expandTaskScreen() {
        $('#project_container').addClass('treeExpand');
        $('#collapse').show();
        isFull = true;
        $(window).trigger('resize');
    }
    // Ext.EventManager.onWindowResize(function () {
    // 	Ext.getCmp("pmstreepanel").setHeight();
    // });
    function filterEmployee(text,e){
        $('li[rel="li-employee"]').each(function(index, element) {
            str=$(this).html();
            str=str.toLowerCase();
            text=text.toLowerCase();
            elm=$(this).attr('id');
            if(str.indexOf(text)==-1) {
                $(this).hide();
                $('div[rel='+elm+']').hide();
            } else {
                $(this).show();
                $('div[rel='+elm+']').show();
            }
        });
    }
    $('#reset-date').click(function(){
        if( !confirm('<?php __('Reset the start and end date of all tasks to match with their phases?') ?>') )
            return false;
    });
    // special task section
    //init state
    var startDate, endDate;
    var listDeletion = [];
    var Task;
    var Holidays = {};  //get by ajax
    var Workdays = <?php echo json_encode($workdays) ?>;

    var monthName = <?php
        echo json_encode(array(
            __('January', true),
            __('February', true),
            __('March', true),
            __('April', true),
            __('May', true),
            __('June', true),
            __('July', true),
            __('August', true),
            __('September', true),
            __('October', true),
            __('November', true),
            __('December', true)
        )); ?>;
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
                unit_price: 0,
                profile_id : '',
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
        $('#nct-milestone option[value="' + me.options.task.milestone_id + '"]').prop('selected', true);
        $('#nct-status option[value="' + me.options.task.task_status_id + '"]').prop('selected', true);
        $('#nct-start-date').val(me.options.task.task_start_date).datepicker( "setDate", me.options.task.task_start_date );
        $('#nct-end-date').val(me.options.task.task_end_date).datepicker( "setDate", me.options.task.task_end_date );
        $('#nct-manual').val(me.options.task.manual_consumed);
        $('#nct-unit-price').val(me.options.task.unit_price);
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
		// set assign 
		me.setAssign();
        /*
        *{ id : number }
        */
		// refresh multiselect assign to
		
		$.each(me.options.employees_actif, function(i, employee){
			if(employee['Employee']['actif'] == 0){
				id_element = '#nct-resources-list-check-'+employee['Employee']['id']+'-'+employee['Employee']['is_profit_center'];
				$(id_element).closest('label').hide();
			}
		});
		
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

        //build data
        var type = 2;
        $.each(data, function(row, items){
            //build row
            var date = row.substr(2);
            var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: left">' + toRowName(row) + '<span class="cancel" onclick="removeRow(this)" href="javascript:;"></span></td>';
            var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
            if( isNaN(c) )c = 0;
            if( isNaN(iu) )iu = 0;
            $.each(items, function(i, item){
                var id = item.reference_id + '-' + item.is_profit_center;
                html += '<td class="value-cell cell-'+id+'"><input type="text" id="val-' + date + '-' + id + '" data-old="' + item.estimated + '" class="workload-'+id+'" data-ref value="' + item.estimated + '" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)"></td>';
                //calculate estimated
                estimate[id] += item.estimated;
                type = item.type;
            });
            //last col
            html += '<td style="background: #f0f0f0" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td></tr>';
            $('#assign-table tbody').append(html);
            if( c > 0 || iu > 0 ){
                $('#date-' + date).find('.cancel').remove();
            }
            //consume += c;
            //inUsed += iu;
        });
        //fill data for footer
        $.each(columns, function(i, col){
            $('#foot-' + col.id).text(estimate[col.id].toFixed(2));
        });
        $('#total-consumed').text(consume.toFixed(2) + ' (' + inUsed.toFixed(2) + ')');
        //disable name if task have consumed/in used
        if( consume > 0 || inUsed > 0 ){
            $('#nct-name').prop('disabled', false);
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
        if(hide_unit){
            $('#wd-unit-price').css('display', 'block');
        }
        if(hide_milestone){
            $('#wd-milestone').css('display', 'block');
        }
        //chon range tai day:
        $('#nct-range-type option[value="' + type +'"]').prop('selected', true);
        selectRange();
        //calculate
        calculateTotal();
        //refresh picker
        refreshPicker();
        // bind keys
        bindNctKeys();
        //done!
    }
	SpecialTask.prototype.setAssign = function(){
		var me = this;
		var $elm = $('#nct-resources-list + .multiSelectOptions');
		var datas = me.options.columns;
        $.each(datas, function (ind, data) {
			$elm.find('input[value="' + data.id + '"]').trigger('click');
        });
		
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
                unit_price: 0,
                profile_id : '',
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
        $('#nct-range-type option[value="1"]').prop('selected', true);
        //$('#add-date').addClass('disabled');
    }
    SpecialTask.prototype.commit = function(){
        //check data
        var name = $.trim($('#nct-name').val()),
            sd = $('#nct-start-date').datepicker('getDate'),
            ed = $('#nct-end-date').datepicker('getDate');
        if( !name ){
            var dialog = $('#modal_dialog_alert1').dialog();
            $('#btnNoAL1').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Error: Please enter task name') ?>');
            return false;
        }
        if( !sd || !ed ){
            var dialog = $('#modal_dialog_alert2').dialog();
            $('#btnNoAL2').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Error: Please pick start date / end date') ?>');
            return false;
        }
        if( sd > ed ){
            alert('<?php __('Error: start date can not be greater than end date') ?>');
            $('#nct-start-date').focus();
            return false;
        }//check date range
        if( !isValidList() ){
            alert('<?php __('Error: There are dates not between start date and end date') ?>');
            return false;
        }
        var result = {
            data: {
                workloads: {},
                id: <?php echo $projectName['Project']['id'] ?>,
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
                    profile_id : $('#nct-profile').val(),
                    manual_consumed: $('#nct-manual').val(),
                    unit_price: $('#nct-unit-price').val(),
                    milestone_id: $('#nct-milestone').val()
                }
            }
        };

        $count_value_cell_hidden = $('#assign-table tbody tr .value-cell').filter(function() {
            return $(this).css('display') == 'none';
        }).length;
        if( !$('#assign-table tbody tr .value-cell input').length ){
            var dialog = $('#modal_dialog_alert').dialog();
            $('#btnNoAL').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Select at least a resource or a team') ?>');
            return false;
        } else if( $count_value_cell_hidden == $('#assign-table tbody tr .value-cell input').length ) {
            var dialog = $('#modal_dialog_alert').dialog();
            $('#btnNoAL').click(function() {
                dialog.dialog('close');
            });
            // alert('<?php __('Select at least a resource or a team') ?>');
            return false;
        }

        $('#assign-table tbody tr').each(function(){
            var tr = $(this),
                date = tr.find('.nct-date').prop('id').replace('date-', '');
            result.data.workloads[date] = {};
            var inputs = tr.find('.value-cell input:visible');
            inputs.each(function(){
                var me = $(this),
                    id = me.prop('class').replace('workload-', ''), t = id.split('-');
                result.data.workloads[date][id] = {
                    reference_id: t[0],
                    estimated: parseFloat(me.val()).toFixed(2),
                    is_profit_center: t[1]
                };
            });
        });
        //console.log(result.data.workloads);return;
        //save
        //disable button first
        var btns = $('#save-special,#cancel-special').addClass('disabled');
        var text = $('#nct-progress').show();
        var tree = $('#special-task-info').data('tree');
        $.ajax({
            url: '<?php echo $this->Html->url('/') ?>project_tasks/saveNcTask',
            type: 'POST',
            dataType: 'json',
            data:  { data: JSON.stringify(result)},
            success: function(response){
                $('#special-task-info').dialog('close');
                tree.setLoading(i18n('Please wait'));
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
                            leaf        : false,
                            expanded    : true,
                            children    : [],
                            parent_id   : 0,
                            parent_name : '',
                            project_id  : tree.project_id,
                            is_nct      : 1
                        }, response.data);
                        var newTask = Ext.create('PMS.model.ProjectTask', task);
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
					
					// tree.setLoading(false);
					// tree.refreshView();
                    tree.refreshSummaryNew(response.data, function(callback){
                    // tree.refreshSummary(function(callback){
						$('.x-grid-cell').removeClass('x-grid-dirty-cell');
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
            if( e.keyCode == 13 ){
                if( $(this).closest('td').next().hasClass('ciu-cell') ){
                    $(this).closest('tr').next().find('input:first').focus();
                } else {
                    $(this).closest('td').next().find('input').focus();
                }
                e.preventDefault();
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
    function removeCol(e){
        //check consume
        var td = $(e).parent();
        var id = td.prop('id').replace('col-', ''), t = id.split('-');
        if( typeof Task.options.consume[id] != 'undefined' && Task.options.consume[id] ){
            alert('<?php __('This resource/PC already has consumed/in used data') ?>');
            return;
        }
        if( confirm('<?php __('Are you sure?') ?>') ){
            $('#col-' + id + ', .cell-' + id + ', #foot-' + id).each(function(){
                $(this).remove();
            });
            $('#res-' + id).show();
            listDeletion.push('res-' + id);
        }
    }

    function dialog_confirm(e, message) {
        $('.title').html(message);
        var dialog = $('#modal_dialog_confirm').dialog();
        $('#btnYes').click(function() {
            dialog.dialog('close');
            removeRowCall(e);
        });
        $('#btnNo').click(function() {
            dialog.dialog('close');
        });
    }
    function removeRowCall(e){
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
        calculateTotal();
    }
    function removeRow(e){
        //check if has in used / consumed
        if( $(e).parent().parent().find('.ciu-cell').text() != '0.00 (0.00)' ){
            alert('<?php __('Can not delete this because it has consumed/in used data') ?>');
            return;
        }
        var check = dialog_confirm(e, '');
		setLimitedDate('#nct-start-date', '#nct-end-date');
    }
    function changeTotal(e){
        //check here
        var me = $(e);
        if( !me.length )return;
        var old = parseFloat(me.data('old'));
        var newVal = parseFloat(me.val());
        var type = parseInt($('#nct-range-type').val());
        if( (isNaN(newVal) || newVal < 0 ) || (type == 0 && newVal > 1) ){
            if( type == 0 )
                alert('<?php __('Enter value between 0 and 1') ?>');
            else alert('<?php __('Please enter value >= 0') ?>');
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

    function selectCurrentRange(){
        //$('#nct-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
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
                $('.period-input').hide();

            break;
            case 3:
                $('.start-end').hide();
                $('#nct-range-picker').hide();
                $('.period-input').show();
                $('#add-range').show();
                $('#reset-range').show();
            break;
            default:
                $('.start-end').show();
                $('.range-picker').hide();
                $('.period-input').hide();
            break;
        }
        if( val ==3){
            $('#create-range').hide();
        } else {
            $('#create-range').show();
        }
        resetRange();
        if( !$('#assign-table tbody tr').length ){
            $('#assign-table tfoot .value-cell').text('0.00');
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
            case '3':
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
	function predecessor_unhightlightTask(){
		$('.x-grid-item').removeClass('item-task-predecessor');
	}
	function predecessor_hightlightTask(taskID){
		$('.x-grid-item').removeClass('item-task-predecessor');
		pre_task = $('.task-' + taskID);
		pre_task.closest('.x-grid-item').addClass('item-task-predecessor');
	}

    $(document).ready(function(){
        $('#clean-filters').click(function(){
            try {
                var panel = Ext.getCmp('pmstreepanel');
                panel.cleanFilters();
            } catch(ex){
            }

            var which = $('.type-focus').data('type');
            if( $('.type-focus').hasClass('type-focus') ){
                $('.filter-type').removeClass('type-focus');
                which = '';
            } else {
                $('.filter-type').removeClass('type-focus');
                $('.type-focus').addClass('type-focus');
            }
            $('#clean-filters').addClass('hidden');
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
        //$('#assign-list').height(Math.round((30*$(window).height())/100));
        $('#nct-start-date').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                return [Workdays[date] == 1, '', ''];
            },
            onSelect: function(){
                //showPicker();
                refreshPicker();
            }
        });
        $('#nct-end-date').datepicker({
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
        });

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
                        var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: left">' + date + '<a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
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

        $('#cancel-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            $('#special-task-info').dialog('close');
        });
        $('#save-special').click(function(){
            if( $(this).hasClass('disabled') )return false;
            Task.commit();
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
						console.log(date);
                    $('<td class="value-cell cell-' + id + '"><input type="text" id="val-' + date + '-' + id + '" data-old="0" class="workload-' + id + '" value="0" style="text-align: right; width: 98%; border: 0; padding: 0" onchange="changeTotal(this)" data-ref></td>').insertBefore(ciu);
                });
                bindNctKeys();
                //add footer
                $('<td class="value-cell cell-' + id + '" id="foot-' + id + '">0.00</td></tr>').insertBefore('#total-consumed');
            } else {
                //remove (or hide), i choose to hide those cells
                cell.hide();
            }
            refreshPicker();
            calculateTotal();
        });
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
                } else if(type == 2 || type == 0){
                    startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
                    endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
                }else {
                    startDate = curStart;
                    endDate = curEnd ;
                }
                //điều chỉnh lại start date / end date của 2 cái input
                if( curStart > startDate ){
                    //$('#nct-start-date').datepicker('setDate', startDate);
                }
                if( curEnd < endDate ){
                    //$('#nct-end-date').datepicker('setDate', endDate);
                }
                var start = dateString(startDate);
                var end = dateString(endDate);
                $('#date-list').multiDatesPicker('resetDates');
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
        $('#per-workload').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
            var _val = parseFloat(val, 10);
            if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
                e.preventDefault();
                return false;
            }
        });
        $(document).ready(function(){
            $('#per-workload').bind("cut copy paste",function(e) {
                e.preventDefault();
            });
        });
        // $("#per-workload").bind("paste", function(e){
        //     // var pastedData = e.originalEvent.clipboardData.getData('text');
        //     var pastedText;
        //     if (window.clipboardData && window.clipboardData.getData) { // IE
        //         pastedText = window.clipboardData.getData('text');
        //     } else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
        //         pastedText = e.originalEvent.clipboardData.getData('text');
        //     }
        //     if( !$.isNumeric(pastedText) || pastedText < 0 ){
        //         e.preventDefault();
        //         return false;
        //     }
        // });
        $('#nct-unit-price').keypress(function(e){
            var key = e.keyCode ? e.keyCode : e.which;
            if(!key || key == 8 || key == 13 || e.ctrlKey){return;}
            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
            var _val = parseFloat(val, 10);
            if(!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,9})(\.[0-9]{0,2})?$/.test(val)){
                e.preventDefault();
                return false;
            }
        });
        //init datepick period
        $('#nct-period-start-date').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                var canSelect = d >= start && Workdays[date] == 1 && d <= end;
                return [canSelect, '', ''];
            },
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                startDate = date;
            },
        });
        $('#nct-period-end-date').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShowDay: function(d){
                var date = d.getDay();
                var start = $('#nct-period-start-date').datepicker('getDate'),
                    end = $('#nct-end-date').datepicker('getDate');
                if( start && end){
                    var canSelect = d >= start && Workdays[date] == 1 && d <= end;
                    return [canSelect, '', ''];
                }
                return [false];
            },
            onSelect: function(dateText, inst) {
                var type = parseInt($('#nct-range-type').val());
                var date = $(this).datepicker('getDate');
                endDate = date;
            },
        });
        $('#nct-range-type').change(function(){
            //xoa het tat ca workload
            $('#assign-table tbody').html('');
            selectRange();
        });
        $('#reset-range').click(function(){
            resetRange();
        });
        $('#add-range').click(function(){
            //check neu co assign thi moi them date
            if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length == 0 ){
                return;
            }
            if( startDate && endDate ){
                _addRange(startDate, endDate);
            }
            $('.period-input-calendar').datepicker('setDate', null);
        });
        $('#create-range').click(function(){
            if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length == 0 ){
                var dialog = $('#modal_dialog_alert').dialog();
                $('#btnNoAL').click(function() {
                    dialog.dialog('close');
                });
                return;
            }
            var dateType = parseInt($('#nct-range-type').val());
            //available for week and month and day.
            var start = $('#nct-start-date').datepicker('getDate'),
                end = $('#nct-end-date').datepicker('getDate');
            if(!start || !end){
                var dialog = $('#modal_dialog_alert2').dialog();
                $('#btnNoAL2').click(function() {
                    dialog.dialog('close');
                });
                return false;
            }
			setLimitedDate('#nct-start-date', '#nct-end-date');
            addRange(start, end);
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
    function getListDay(start, end){
        var list = {};
        var current = new Date(start);
        while( current <= end ){
            var sm = new Date(current);
            var currentDate = sm.format('yymmdd');
            if( typeof list[currentDate] == 'undefined' ){
                list[currentDate] = {
                    // start: monday,
                    date: new Date(sm.getFullYear(), sm.getMonth(), sm.getDate())
                };
            }
            current = new Date(sm.getFullYear(), sm.getMonth(), sm.getDate()+1);
        }
        return list;
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
        var list;
        if( type == 0 ) {
            list = getListDay(start, end)
        } else if (type == 1) {
            list = getListWeek(start, end)
        } else {
            list = getListMonth(start, end)
        }
        // var list = type == 1 ? getListWeek(start, end) : getListMonth(start, end),
        var minDate, maxDate;
        $.each(list, function(key, date){
            if(type == 0 ){
                if( !minDate || minDate >= date.date ){
                    minDate = new Date(date.date);
                }
                if( !maxDate || maxDate <= date.date ){
                    maxDate = new Date(date.date);
                }
                var _curYear = date.date.format('yy'),
                    _curDate = date.date.format('dd-mm-yy')
                if(Holidays[_curYear].length == 0){
                    loadHolidays(_curYear);
                }
                if( ($.inArray(_curDate, Holidays[_curYear]) == -1) && isValidDateForNctTaskDate(date.date) ){
                    _addRange(date.date, date.date);
                }
            } else {
                if( !minDate || minDate >= date.start ){
                    minDate = new Date(date.start);
                }
                if( !maxDate || maxDate <= date.end ){
                    maxDate = new Date(date.end);
                }
                _addRange(date.start, date.end);
            }
        });
        $('#nct-start-date').datepicker('setDate', minDate);
        $('#nct-end-date').datepicker('setDate', maxDate);
    }
    function isValidDateForNctTaskDate(d){
        var date = d.getDay();
        return Workdays[date] == 1;
    }
    function _addRange(start, end){
        var date = dateString(start) + '_' + dateString(end);
        var type = parseInt($('#nct-range-type').val());
        var rowName;
        if(type == 0){
            date = dateString(start);
            rowName = start.format('dd-mm-yy');
        } else if (type == 1) {
            rowName = start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        }else{
            rowName = monthName[start.getMonth()] + ' ' + start.getFullYear();
        }
        // var rowName = type == 2 ? monthName[start.getMonth()] + ' ' + start.getFullYear() : start.format('dd/mm') + ' - ' + end.format('dd/mm/yy');
        var invalid = $('#date-' + date).length ? true : false;
        if( !invalid ){
            //add new row
            var html = '<tr><td id="date-' + date + '" class="nct-date" style="text-align: left">' + rowName + '<a class="cancel" onclick="removeRow(this)" href="javascript:;"></a></td>';
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
    function addNewComment (){
      var text = $('.text-textarea').val(),
        _id = $('.submit-btn-msg').data('id');
        if(text != ''){
            var _html = '';
            $.ajax({
                url: '/project_tasks/update_text',
                type: 'POST',
                data: {
                    data:{
                        id: _id,
                        text_1: text
                    }
                },
                dataType: 'json',
                success: function(data){
                    if(data){
                        var idEm =  data['_idEm'],
                        avartarImage = listAvartar[idEm],
                        nameEmloyee = data['text_updater'],
                        comment = data['comment'],
                        created = data['text_time'];
                        _html += '<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+ avartarImage +'"></div><div class="content-comment"><div class="name"><h5>'+ nameEmloyee +'</h5><em>'+ created +'</em></div><div class="comment">'+ comment +'</div></div></div>';
                        $('#content-comment-id').append(_html);
                        $('.text-textarea').val("");
						// update read status
						var panel = Ext.getCmp('pmstreepanel');
						panel.setLoading(i18n('Please wait'));
						panel.getStore().getById(_id).set('text_1', 1);
						panel.getView().refresh();
						panel.setLoading(false);
                    }
                }
            });
        }
    }
</script>
<div id="special-task-info" style="display:none; float: none;" class="buttons">
    <div class="w">
        <input type="hidden" id="nct-id" class="text" />
        <input type="hidden" id="nct-phase-id" class="text" />
        <div class="wd-input">
            <label><?php echo __d(sprintf($_domain, 'Project_Task'), 'Task', true);?></label>
            <input type="text" id="nct-name" class="text" style="width: 250px" />
        </div>
        <?php $priority = json_decode($listPrioritiesJson, true);?>
        <div class="wd-input">
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
            <select id="nct-status">
                <!--option value=""><?php __d(sprintf($_domain, 'Project_Task'), 'Status') ?></option -->
                <?php
                    foreach($priority['Statuses'] as $p){
                        echo '<option value="' . $p['ProjectStatus']['id'] . '">' . $p['ProjectStatus']['name'] . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="wd-input">
            <select id="nct-profile">
                <option value="" style="font-weight: bold"><?php __d(sprintf($_domain, 'Project_Task'), 'Profile') ?></option>
                <?php
                    foreach($priority['Profiles'] as $p){
                        echo '<option value="' . $p['Profile']['id'] . '">' . $p['Profile']['name'] . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="wd-input" id="wd-milestone" style="display: none">
            <select id="nct-milestone">
                <option value=""><?php __('Milestone') ?></option>
                <?php
                    foreach($priority['Milestone'] as $p){
                        echo '<option value="' . $p['ProjectMilestone']['id'] . '">' . $p['ProjectMilestone']['project_milestone'] . '</option>';
                    }
                ?>
            </select>
       </div>
        <div class="wd-input">
            <select id="nct-range-type">
                <option value="0"><?php __('Day') ?></option>
                <option value="1" selected><?php __('Week') ?></option>
                <option value="2"><?php __('Month') ?></option>
                <option value="3"><?php __('Period') ?></option>
            </select>
        </div>
        <hr>
        <div class="wd-input" style="clear: both;">
            <label style="margin-top: 5px;margin-right: 5px"><?php echo __d(sprintf($_domain, 'Project_Task'), 'Assigned To', true);?></label>
            <select id="nct-resources-list" multiple>
                <option value=""><?php __('- Select -') ?></option>
                <?php
                    foreach($priority['Employees'] as $p){
                        echo '<option value="' . $p['Employee']['id'] . '-' . $p['Employee']['is_profit_center'] . '" id="res-' . $p['Employee']['id'] . '-' . $p['Employee']['is_profit_center'] . '" data-name="' . $p['Employee']['name'] . '">' . $p['Employee']['name'] . '</option>';
                    }
                ?>
            </select>

            <?php if( !empty($ManualConsumed) && $ManualConsumed ): ?>
                <label style="width: auto"><?php __d(sprintf($_domain, 'Project_Task'), 'Manual Consumed') ?></label>
                <input type="text" id="nct-manual" class="text" value="0" style="width: 100px" />
            <?php endif ?>
        </div>
        <div class="wd-input" id="wd-unit-price" style="margin-left: 10px; display: none">
            <label><?php echo __('Unit Price', true) ?></label>
            <input type="text" id="nct-unit-price" class="text" style="width: 50px" />
        </div>
        <hr>
        <div class="wd-input">
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'Start date') ?></label>
            <input type="text" id="nct-start-date" class="text" style="width: 80px" />
        </div>
        <div class="wd-input">
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'End date') ?></label>
            <input type="text" id="nct-end-date" class="text" style="width: 80px" />
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
            <div class="period-input">
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'Start date') ?></label>
            <input type="text" id="nct-period-start-date" class="text period-input-calendar nct-date-period" readonly="readonly"  style="width: 120px" />
            <br>
            <label><?php __d(sprintf($_domain, 'Project_Task'), 'End date') ?></label>
            <input type="text" id="nct-period-end-date" class="text period-input-calendar nct-date-period" readonly="readonly"  style="width: 120px" />
            <br>
            </div>
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
        <div id="assign-list" style="float: left; width: 800px; margin-right: 30px; margin-bottom: 5px; overflow-x: auto; overflow-y: auto; max-height: 450px;">
            <table id="assign-table">
                <thead>
                    <tr>
                        <td class="bold base-cell null-cell">&nbsp;</td>
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
<?php
$listPrioritiesJson = json_decode($listPrioritiesJson, true);
$listProjectStatus = !empty($listPrioritiesJson) ? Set::classicExtract($listPrioritiesJson['Statuses'], '{n}.ProjectStatus') : array();

 ?>
<script>
var project_id = <?php echo json_decode($project_id)?>;
var listAllStatus = <?php echo json_encode($listProjectStatus)?>;
var budgetCurrency = <?php echo json_encode($bg_currency)?>;
var api_key = <?php echo json_encode($api_key) ?>;
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
    if( hlds.length ){
        $('#date-list').multiDatesPicker('addDates', hlds, 'disabled');
    }
}
function refreshPicker(){
    var start = $('#nct-start-date').datepicker('getDate'),
        end = $('#nct-end-date').datepicker('getDate');
    if( start && end && start <= end ){
        $('#date-list').datepicker('setDate', start);
        if( $('#nct-resources-list + .multiSelectOptions').find('INPUT:checkbox:checked').length > 0 ){
            $('#add-date').removeClass('disabled');
            $('#add-range').removeClass('disabled');
        }
        resetRange();
        $('#nct-period-start-date, #nct-period-end-date').prop('disabled', false);
    }
    else {
        $('#add-date').addClass('disabled');
        $('#add-range').addClass('disabled');
        $('#nct-period-start-date, #nct-period-end-date').prop('disabled', true);
    }
    if( !start ) year = new Date().getFullYear();
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
function openAttachmentDialog(){
    if( !canModify )return false;
    var me = $(this), id = me.prop('alt');
	var has_attch = 0;
    var _html = task_title = '';
    var latest_update = '';
    $('#UploadId').val(id);
    var popup = $('#template_attach');
    $.ajax({
        url: '/kanban/getTaskAttachment/'+ id,
        type: 'POST',
        data: {
            id: id,
        },
        dataType: 'json',
        success: function(data) {
            $("#template_upload").addClass('show');
            $('.light-popup').addClass('show');
            $("#template_upload").find('.project-name').empty().append(data['task_title']);
            _html += '<ul>';
            if (data['attachments']) {
                $.each(data['attachments'], function(ind, _data) {
					has_attch = 1;
					if( _data['ProjectTaskAttachment']['is_old_attachment'] == 1){
						var att = _data['ProjectTaskAttachment']['attachment'].split(':');
						_html += '<li class="old-attachment">';
						if( att[0] == 'file' ) {
							_html += '<i class="icon-paper-clip"></i><a class="file-name"  href="javascript:void(0);" onclick="openAttachment.call(this)" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('file:','') +'</a>';
						} else {
							_html += '<i class="icon-link"></i><a class="file-name"  href="' + _data['ProjectTaskAttachment']['attachment'].replace('url:','') + '" target="_blank" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('url:','') +'</a>';
						}
						_html += '<a href="javascript:void(0);" data-id = "'+ id +'" alt="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ id +'" onclick="deleteAttachment.call(this)"></a>';
						_html += '</li>';
					}else{
						if(_data['ProjectTaskAttachment']['is_file'] == 1){
							if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
								_html += '<li><i class="icon-paper-clip"></i><span class="file-name" onclick="openFileAttach.call(this);" data-id = "'+ _data['ProjectTaskAttachment']['id'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
							}else{
								_link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?download=1&?sid='+ api_key;
								_html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
							}
						}else{
							_html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
						}
                    }
                });
            }
            _html += '</ul>';
            $('#content_comment .append-comment').empty().html(_html);
			if( has_attch){
				var panel = Ext.getCmp('pmstreepanel');
				panel.setLoading(i18n('Please wait'));
				panel.getStore().getById(id).set('attachment', 1);
				panel.getStore().getById(id).set('attach_read_status', 1);
				panel.getView().refresh();
                panel.setLoading(false);
			}

            // $("#dialog_attachement_or_url").dialog('option',{title:data.task_title, width: '320px'}).dialog('open');

        }
    });
}
function openCommentDialog(){
    if( !canModify )return false;
    var me = $(this), pTaskId = me.prop('alt');
	var has_comment = 0;
    // var pTaskId = record.id;
    $.ajax({
        url: '/project_tasks/getCommentTxt',
        data: {
            pTaskId: pTaskId
        },
        type:'POST',
        async: false,
        dataType: 'json',
        success: function(data){
            // console.log(data);
            $("#comment_popup").addClass('show');
            $("#comment_popup").find('.task-name').empty().append(data['task_title']);
            $('.light-popup').addClass('show');
            var _html = '<div id="content-comment-id">';
            $('#win-statusbar').hide();
			if(data['old_comment']){
				var _cm = data['old_comment']['ProjectTask'];
				_html += '<div class="content">';
				_html += '<div class = "avartar-text" style=" width: 60px"><span class="avartar-image-img"><span>' + _cm['avt'] + '</span></span></div>';
				_html += '<div class="content-comment"><div class="name"><h5>' +_cm['text_updater'] + '</h5><em>'+ _cm['text_time'] + '</em></div><div class="comment">'+ _cm['text_1']+'</div></div>'
				_html += '</div>';
				if(_cm && !has_comment) has_comment = 1;
			}
            if(data['result']){
                $.each(data['result'], function(ind, val){
					
                    var employeeId = val['ProjectTaskTxt']['employee_id'],
                        avartarImage = listAvartar[employeeId],
                        nameEmloyee = listEmployee[employeeId],
                        comment = val['ProjectTaskTxt']['comment'].replace(/\n/g, "<br>"),
                        created = val['ProjectTaskTxt']['created'];
					if(comment && !has_comment) has_comment = 1;
                    _html += '<div class="content">';
                    _html += '<div class = "avartar-image" style=" width: 60px"><img class="avartar-image-img" src="'+avartarImage+'"></div>';
                    _html += '<div class="content-comment"><div class="name"><h5>' +nameEmloyee+ '</h5><em>'+created+'</em></div><div class="comment">'+comment+'</div></div>'
                    _html += '</div>';
                });
				
            }
			// update read status
			if( has_comment ){
				var panel = Ext.getCmp('pmstreepanel');
				panel.setLoading(i18n('Please wait'));
				panel.getStore().getById(pTaskId).set('read_status', 1);
				panel.getView().refresh();
				panel.setLoading(false);
			}
            _html += '</div>';
            _html += '<button data-id="'+data['id']+'" onclick="addNewComment()" class="submit-btn-msg" type="button"><img src="/img/ui/blank-plus.png" alt=""></button><textarea class="text-textarea"></textarea>';
            $("#comment_popup").find('.append-comment').empty().append(_html);
		}			
    });
}
function openAttachment(){
    if( !canModify )return false;
    var me = $(this), taskId = me.attr('alt');
    window.open('<?php echo $this->Html->url('/project_tasks/view_attachment/') ?>' + taskId, '_blank');
}
function deleteAttachment(){
    if( !canModify )return false;
    var me = $(this), taskId = me.prop('alt');
	console.log( me);
	var itemPic = $(this).closest('li');
	var itemList = $('#content_comment .append-comment ul');
    if( confirm('<?php __('Delete?') ?>') ){
        var panel = Ext.getCmp('pmstreepanel');
        panel.setLoading(i18n('Please wait'));
        //call ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/project_tasks/delete_attachment/') ?>' + taskId,
            complete: function(){
				itemPic.remove();
				if( (!itemList.length) || itemList.is(':empty') ){
					panel.getStore().getById(taskId).set('attachment', 0);
				}
                panel.getView().refresh();
                panel.setLoading(false);
            }
        })
    }
}
function openFileAttach(){
    _id = $(this).data("id");
    $.ajax({
        url : "/kanban/ajax/"+ _id,
        type: "GET",
        cache: false,
        success: function (html) {
            var dump = $('<div />').append(html);
            if( dump.children('.error').length == 1 ){
                //do nothing
            } else if ( dump.children('#attachment-type').val() ) {
                $('#contentDialog').html(html);
                $('#dialogDetailValue').addClass('popup-upload');
                showMe();
            }
        }
    });

}
function setLimitedDate(ele_start, ele_end){
	var limit_sdate = $('#assign-table tbody tr:first').find('.nct-date').attr('id');
	if(limit_sdate){
		 limit_start_date = (limit_sdate.split("_")[1]).split("-");
		 limit_s_date = new Date(limit_start_date[1] +'-'+limit_start_date[0] +'-'+limit_start_date[2]);
		 $(ele_start).datepicker('option','maxDate',limit_s_date);
	}
	limit_edate = $('#assign-table tbody tr:last').find('.nct-date').attr('id');
	if(limit_edate){
		 limit_end_date = (limit_edate.split("_")[0]).split("-");
		 limit_e_date = new Date(limit_end_date[2] +'-'+limit_end_date[1] +'-'+limit_end_date[3]);
		  $(ele_end).datepicker('option','minDate',limit_e_date);
	}
}

function deleteAttachmentFile(){
    var taskId = $('#UploadId').val();
    var attachId = $(this).prop('alt');
    var itemPic = $(this).closest('li');
	var itemList = $('#content_comment .append-comment ul');
    $.ajax({
        url: '<?php echo $this->Html->url('/kanban/delete_attachment/') ?>' + attachId,
        success: function (data) {
            itemPic.remove();
			if( (!itemList.length) || itemList.is(':empty') ){
				var panel = Ext.getCmp('pmstreepanel');
				panel.setLoading(i18n('Please wait'));
				panel.getStore().getById(taskId).set('attachment', 0);
				panel.getView().refresh();
				panel.setLoading(false);
			}
        }
    })
}

function syncX(file){
    $.ajax({
        url: '<?php echo $this->Html->url('/project_tasks/syncX') ?>',
        type: 'POST',
        data: {data: {file: file}}
    });
}
    var today = new Date('<?php echo date('Y-m-d') ?>');
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
        });
        var target = jQuery('.gantt-chart-wrapper').find('#month_' + (today.getMonth() + 1) + '_' + today.getFullYear());
        if( target.length ){
            jQuery('.gantt-chart-wrapper').scrollTo( target, true, null );
        }
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
        $("#ok_attachs").click(function(){
            if( isSaving )return false;
            isSaving = true;
            $('.cancel, .new').addClass('grayscale');
            $('#action-attach-url').css('display', 'none');
            $('.browse').css('display', 'block');
            var form = $("#form_dialog_attachement_or_url");

            var panel = Ext.getCmp('pmstreepanel'),
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
        
		$("#ok_attach").on('click',function(){
			id = $('input[name="data[Upload][id]"]').data('id');
			url = $.trim($('input[name="data[Upload][url]"]').val());
			var form = $("#UploadIndexForm");
			if(url){
				form.submit();
			}
			
		});
    });

    Dropzone.autoDiscover = false;
    $(function() {
		var myDropzone = new Dropzone("#upload-popup", {
			acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
		});
        myDropzone.on("queuecomplete", function(file) {
            id = $('#UploadId').val();
			var has_attch = 0;
            $.ajax({
                url: '/kanban/getTaskAttachment/'+ id,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    _html = '<ul>';
                    if (data['attachments']) {
                        $.each(data['attachments'], function(ind, _data) {
							has_attch = 1;
							if( _data['ProjectTaskAttachment']['is_old_attachment'] == 1){
								var att = _data['ProjectTaskAttachment']['attachment'].split(':');
								_html += '<li class="old-attachment">';
								if( att[0] == 'file' ) {
									_html += '<i class="icon-paper-clip"></i><a class="file-name"  href="javascript:void(0);" onclick="openAttachment.call(this)" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('file:','') +'</a>';
								} else {
									_html += '<i class="icon-link"></i><a class="file-name"  href="' + _data['ProjectTaskAttachment']['attachment'].replace('url:','') + '" target="_blank" alt="' + id + '">'+ _data['ProjectTaskAttachment']['attachment'].replace('url:','') +'</a>';
								}
								_html += '<a href="javascript:void(0);" data-id = "'+ id +'" alt="' + id + '"><img src="/img/new-icon/delete-attachment.png" alt="'+ id +'" onclick="deleteAttachment.call(this)"></a>';
								_html += '</li>';
							}else{
								if(_data['ProjectTaskAttachment']['is_file'] == 1){
									if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(_data['ProjectTaskAttachment']['attachment'])){ 
										_html += '<li><i class="icon-paper-clip"></i><span class="file-name" onclick="openFileAttach.call(this);" data-id = "'+ _data['ProjectTaskAttachment']['id'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</span><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
									}else{
										_link = '/kanban/attachment/'+ _data['ProjectTaskAttachment']['id'] +'/?download=1&?sid='+ api_key;
										_html += '<li><i class="icon-paper-clip"></i><a class="file-name"  href="'+ _link +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
									}
								}else{
									_html += '<li><i class="icon-link"></i><a class="file-name" target="_blank" href="'+ _data['ProjectTaskAttachment']['attachment'] +'">'+ _data['ProjectTaskAttachment']['attachment'] +'</a><a  data-id = "'+ _data['ProjectTaskAttachment']['id'] +'"><img src="/img/new-icon/delete-attachment.png" alt="'+ _data['ProjectTaskAttachment']['id'] +'" onclick="deleteAttachmentFile.call(this)"></a></li>';
								}
							}
						});
                    }
                    _html += '</ul>';
                    $('#content_comment .append-comment').empty().append(_html);
					if( has_attch){
						var panel = Ext.getCmp('pmstreepanel');
						panel.setLoading(i18n('Please wait'));
						panel.getStore().getById(id).set('attachment', 1);
						panel.getStore().getById(id).set('attach_read_status', 1);
						panel.getView().refresh();
						panel.setLoading(false);
					}
                }
            });
        });
        myDropzone.on("success", function(file) {
            myDropzone.removeFile(file);
        });
		$('#UploadIndexForm').on('submit', function(e){
            $('#UploadIndexForm').parent('.wd-popup').addClass('loading');
            // return;
            if(myDropzone.files.length){
                e.preventDefault();
                popupDropzone.processQueue();
            }
        });
        myDropzone.on('sending', function(file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $('#UploadIndexForm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
    });
 
    setTimeout(function(){
        cleanFilter();
    }, 3000);
    function cleanFilter(){
        var check = false;
        if($('.type-focus').length > 0 || $('.has-filter').length > 0 || $('.x-icon-filtered').length > 0){
            check = true;
        }
        if(check === true){
            $('#clean-filters').removeClass('hidden');
        } else {
            $('#clean-filters').addClass('hidden');
        }
    }
</script>

<div id="collapse" style="padding:4px; cursor:pointer; background-color:#FFF; display:none; position: fixed; top:0; right:0; z-index:9999999999" onclick="collapseTaskScreen();" >
    <button class="btn btn-esc"></button>
</div>
