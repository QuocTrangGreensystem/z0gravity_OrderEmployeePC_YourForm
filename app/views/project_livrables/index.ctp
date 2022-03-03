<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'multipleUpload/jquery.plupload.queue',
    'slick_grid/slick.edit'
));
// echo $html->script('d3/pdc.js');
echo $this->Html->script(array(
    'history_filter',
    'jquery.multiSelect',
    'responsive_table',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/lib/jquery.event.drop-2.0.min',
    'slick_grid/lib/jquery.event.drag-2.2',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.rowselectionmodel',
    'slick_grid/plugins/slick.rowmovemanager',
    'slick_grid/slick.editors',
    'slick_grid/slick.grid',
    'slick_grid_custom',
    'slick_grid/slick.grid.activity',
    'jquery.ui.touch-punch.min',
    'multipleUpload/plupload.full.min',
    'multipleUpload/jquery.plupload.queue',
    'd3/d3.min',
    'd3/pdc',
));
echo $this->element('dialog_projects');
?>
<style>
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    p {
        margin-bottom: 10px;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
    .slick-viewport-right{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
    .slick-headerrow .slick-headerrow-column{
        border-right: none;
    }
    .slick-cell{
        border-right: none;
    }
    .slick-pane-left > .ui-state-default .slick-header-column:first-child{
        border-right: none;
    }
    .slick-pane-right > .ui-state-default .slick-header-column:first-child{
        border-left: 1px solid #fff;
    }
    #comment-ct{
        max-height: 500px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    #template_comment{
        height: 550px !important;
        width: 500px !important;
    }
    .ui-dialog .ui-dialog-titlebar{
        background-color:  #fff;
        padding: 10px;
        border-bottom: 1px solid #e5e5e5;
    }
    .ui-dialog .ui-dialog-titlebar .ui-dialog-titlebar-close{
        top: 18px;
    }
   
    .add-comment{
        display: block;
    }
    .add-comment textarea{
        width: calc(100% - 68px);
        padding: 8px;
        line-height:1.5;
        font:13px Tahoma, cursive;
        max-height: 400px;
        overflow-x: hidden;
        overflow-y: auto;
        display: inline-block;
    }
    .submit-btn-msg{
        width: 50px;
        height: 50px;
        border: none;
        vertical-align: top;
        color: #fff;
        position: relative;
        font-size: 20px;
        background-color: #56aaff !important;
        cursor: pointer;
        display: inline-block;
    }
    #content_comment{
        padding-top: 10px;
        height: 500px;
        overflow-y: scroll;
    }
    .delete-attachment, .download-attachment, .url-attachment{
        width: 20px;
        height: 20px;
    }
    .url-attachment{
        /*background-image: url("/img/extjs/icon-url-larger.png") no-repeat top right;*/
        background: url("/img/extjs/icon-url-larger.png") no-repeat top right;
    }
    .download-attachment{
        background-image: url("/img/extjs/icon-task-folder-larger.png");
    }
    .delete-attachment{
        background-image: url("/img/delete-larger.png");
    }
    .plupload_container{
        padding: 0px;
        width: 95%;
    }
    .plupload_scroll .plupload_filelist{
        height: 150px;
    }
    li.plupload_droptext{
        line-height: 100px;
    }
    .plupload_filelist_header, .plupload_filelist_footer{
        display: none;
    }
    .plupload_scroll .plupload_filelist{
        height: 29px;
        overflow: hidden;
    }
    li.plupload_droptext{
        line-height: 29px;
        color: #fff;
    }
    .plupload_filelist li{
        display: none;
    }
    .download-attachment{
        float: right;
        position: relative;
        top: 6px;
    }
    .comment{
        margin-top: 5px;
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
    .avartar-image{
        display: inline-block;
        vertical-align: top;
        margin-top: 2px;
    }
    .avartar-image-img {
        width: 28px;
        height: 28px;
        margin: 0 10px;
        padding: 5px;
        border: 1px solid #bbb;
        border-radius: 3px;
    }  
    .content-comment {
        display: inline-block;
        width: calc( 100% - 65px);
    } 
    #content_comment .content{
        margin-bottom: 20px
    }
    .comment {
        margin-top: 5px;
        width: 98%;
        height: 16px;
        background: #fff;
        border: 1px solid transparent;
        transition: border-color 0.5s;
        font-size: 13px;
        color:#000;
    }
    .ui-dialog .ui-dialog-titlebar .ui-dialog-title{
        color: #000;
    }
    #gs-url{
        top: 120px;
    }
    #gs-attach{
        top: 80px;
    }
    .btn-disable{
        display: none;
    }
	.wd-tab{
		max-width: 1920px;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_livrables', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <a href="javascript:void(0);" class="export-excel-icon-all" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px;" onclick="addNewDeliverablesButton();" title="<?php __('Add an item') ?>"></a>
                    <?php if($employee_info['Role']['name'] == 'admin'): ?>
                    <a target="_blank" href="<?php echo $html->url("/project_livrable_categories/index/ajax") ?>" id="button-setting" class="button-setting" title="<?php __('Setting')?>"></a>
                    <?php endif; ?>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                    <input type="checkbox" class="not_save_history" style="margin-top: 10px" name="hidden" <?php echo (!empty($saveHistory) && $saveHistory['HistoryFilter']['params'] == 'true') ? 'checked="checked"' : '' ?> id="hidden_checkbox">
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
                <div class="wd-table" id="project_container" style="width:100%; height: 400px">

                </div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
        </div>
    </div>
</div>
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

$columns1 = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        'cssClass' => 'slick-cell-move-handler',
        'formatter' => 'Slick.Formatters.livrableIcon'
    ),
    array(
        'id' => 'livrable_drag',
        'field' => 'livrable_drag',
        'name' => '',
        'width' => 90,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        'formatter' => 'Slick.Formatters.livrableDrag'
    ),
    array(
        'id' => 'livrable_file_attachment',
        'field' => 'livrable_file_attachment',
        'name' => '',
        'width' => 70,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        // 'editor' => 'Slick.Editors.livrableAttachment',
        'formatter' => 'Slick.Formatters.livrableAttachment'
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'project_livrable_comment',
        'field' => 'project_livrable_comment',
        'name' => '',
        'width' => 40,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        'formatter' => 'Slick.Formatters.livrableComment'
    ),
);
$columns2 = array(
    array(
        'id' => 'project_code',
        'field' => 'project_code',
        'name' => __('Project Code', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
    ),
);
$columns3 = array(
    array(
        'id' => 'project_livrable_category_id',
        'field' => 'project_livrable_category_id',
        'name' => __('Deliverable', true),
        'width' => 250,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'project_livrable_status_id',
        'field' => 'project_livrable_status_id',
        'name' => __('Status', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'livrable_progression',
        'field' => 'livrable_progression',
        'name' => '',
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.percentValue',
        'formatter' => 'Slick.Formatters.percentValues'
    ),
    array(
        'id' => 'version',
        'field' => 'version',
        'name' => __('Version', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'actor_list',
        'field' => 'actor_list',
        'name' => __('Actor', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox'
    ),
);
$i = 1;
$columns4 = array(
    array(
        'id' => 'livrable_date_modify',
        'field' => 'livrable_date_modify',
        'name' => __('Date', true),
        'width' => 150,
        'datatype' => 'datetime',
        'sortable' => false,
        'resizable' => true,
        // 'editor' => 'Slick.Editors.datePicker',
        // 'validator' => 'DateValidate.deliveryDate'
    ),
    array(
        'id' => 'livrable_time_modify',
        'field' => 'livrable_time_modify',
        'name' => __('Time', true),
        'width' => 150,
        'datatype' => 'datetime',
        'sortable' => false,
        'resizable' => true,
        // 'editor' => 'Slick.Editors.datePicker',
        // 'validator' => 'DateValidate.deliveryDate'
    ),
    array(
        'id' => 'id',
        'field' => 'id',
        'name' => __('Unique Id', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
    ),
    array(
        'id' => 'employee_modify',
        'field' => 'employee_modify',
        'name' => __('First and last name', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        // 'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'team',
        'field' => 'team',
        'name' => __('Team', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        // 'editor' => 'Slick.Editors.mselectBox'
    ),
);
$columns5 = array(
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
if(!empty($saveHistory) && $saveHistory['HistoryFilter']['params'] == 'true'){
    $columns = array_merge($columns1, $columns2, $columns3, $columns4, $columns5);
} else {
    $columns = array_merge($columns1, $columns3, $columns5);
}
$dataView = array();
$selectMaps = array(
    'project_livrable_category_id' => $livrableCategories,
    'project_livrable_status_id' => $projectStatuses,
    'livrable_responsible' => $employees,
    'actor_list' => $employees
);

foreach ($projectLivrables as $projectLivrable) {
    $data = array(
        'id' => $projectLivrable['ProjectLivrable']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['name'] = $projectLivrable['ProjectLivrable']['name'];
    $data['project_livrable_category_id'] = !empty( $projectLivrable['ProjectLivrable']['project_livrable_category_id']) ? $projectLivrable['ProjectLivrable']['project_livrable_category_id'] : '';
    $data['project_livrable_status_id'] = $projectLivrable['ProjectLivrable']['project_livrable_status_id'];
    $data['livrable_responsible'] = $projectLivrable['ProjectLivrable']['livrable_responsible'];
    $data['project_code'] = $projectCode['Project']['project_code_1'];

    $data['livrable_progression'] = $projectLivrable['ProjectLivrable']['livrable_progression'];
    $data['weight'] = $projectLivrable['ProjectLivrable']['weight'];
    $data['version'] = $projectLivrable['ProjectLivrable']['version'];
    $data['employee_modify'] = !empty($projectLivrable['ProjectLivrable']['employee_id_upload']) && !empty($listEm[$projectLivrable['ProjectLivrable']['employee_id_upload']]) ? $listEm[$projectLivrable['ProjectLivrable']['employee_id_upload']] : '';
    $data['team'] = !empty($projectLivrable['ProjectLivrable']['employee_id_upload']) && !empty($listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]) && !empty($listPc[$listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]]) ? $listPc[$listEmIdOfPc[$projectLivrable['ProjectLivrable']['employee_id_upload']]] : '';

    $data['actor_list'] = Set::classicExtract($projectLivrable['ProjectLivrableActor'], '{n}.Employee.id');

    $data['livrable_date_modify'] = date('d-m-Y', $projectLivrable['ProjectLivrable']['updated']);
    $data['livrable_time_modify'] = date('H:i:s', $projectLivrable['ProjectLivrable']['updated']);

    $data['livrable_file_attachment'] = $projectLivrable['ProjectLivrable']['livrable_file_attachment'];
    $data['format'] = (string) $projectLivrable['ProjectLivrable']['format'];
    $data['upload_date'] = (string) !empty($projectLivrable['ProjectLivrable']['upload_date']) ? date('d-m-Y H:i:s', $projectLivrable['ProjectLivrable']['upload_date']) : '';

    $data['action.'] = '';

    $dataView[] = $data;
}
$projectName['Project']['start_date'] = $str_utility->convertToVNDate($projectName['Project']['start_date']);
$projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['end_date']);
if ($projectName['Project']['end_date'] == "" || $projectName['Project']['end_date'] == '0000-00-00') {
    $projectName['Project']['end_date'] = $str_utility->convertToVNDate($projectName['Project']['planed_end_date']);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true),
    'Cannot leave because upload file in processing!' => __('Cannot leave because upload file in processing!', true)
);
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
        <!-- <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; -->
        <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="template-local-file" style="display: none;">
    <div id="local_directory_link">

    </div>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => 'project_livrables', 'action' => 'upload', $projectName['Project']['id'])
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
        <li  class="btn-disable item-toggle"><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
<div id="dialog_attachement_drag" class="buttons" style="display: none;">
    <?php
    echo $this->Form->create('Upload', array(
            'type' => 'file', 'class' => 'form_dialog_attachement_drag',
            'url' => array('controller' => 'project_livrables', 'action' => 'upload', $projectName['Project']['id'])
        ));
    echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
    echo $this->Form->input('drag', array('type' => 'file', 'value' => '',
        'name' => 'FileField[attachment]',
        'label' => false,
        'value' => '',
        'class' => 'update_drag_class',
        'draggable' => "true",
        'rel' => 'no-history'));
    echo $this->Form->end();
    ?>
</div>
<!-- dialog_attachement_or_url.end -->
<div id="template_comment" style="height: 650px; width: 500px;display: none;">
    <div id="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    <div class="add-comment"></div>
</div>
<?php
$listAvartar = array();
$listIdEm = array_keys($listEm);
foreach ($listIdEm as $_id) {
    $link = $this->UserFile->avatar($_id, "small");
    $listAvartar[$_id] = $link;
}
?>
<script type="text/javascript">
var livrableIcon = <?php echo json_encode($livrableIcon) ?>;
var wdTable = $('.wd-table');
var listAvartar = <?php echo json_encode($listAvartar) ?>;
var employee_id = <?php echo json_encode($employee_id); ?>;
var listId = [];
var _project_id = <?php echo json_encode($projectName['Project']['id']) ?>;
var projectFiles1 = {};

var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 550) ? 550 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
   // heightTable = (heightTable < 550) ? 550 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
    var DateValidate = {},dataGrid,IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);

    };
    (function($){

        $(function(){
            function number_format(number, decimals, dec_point, thousands_sep) {
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
            var $this = SlickGridCustom ,
            uploadForm = $('#upload-template');

            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = dataGrid.getDataItem(row);
                if(data && confirm($this.t(<?php echo json_encode(h(sprintf(__('Delete?', true), '%3$s'))) ?>))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['livrable_file_attachment'] = '';

                    dataGrid.updateRow(row);
                }
                return false;
            });

            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode((!empty($canModified)  && !$_isProfile ) || ($_isProfile && $_canWrite)); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

            DateValidate.startDate = function(value){
                value = getTime(value);
                if(projectName['start_date'] == ''){
                    _valid = true;
                    _message = '';
                } else {
                    _valid = value >= getTime(projectName['start_date']);
                    _message = $this.t('Date closing must larger %1$s' ,projectName['start_date']);
                }
                return {
                    valid : _valid,
                    message : _message
                };
            }
            var status = "";
            if(!$this.canModified){
                $('#attachment-template').find('a.delete-attachment').remove();
                status = "disabled";
            }

            var actionTemplate =  $('#action-template').html(),
            attachmentTemplate =  $('#attachment-template').html(),
            attachmentDragTemplate = $('#dialog_attachement_drag').html(),
            attachmentURLTemplate =  $('#attachment-template-url').html();


            $.extend(Slick.Formatters,{
                livrableAttachment : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 2 || dataContext.format == 1 || dataContext.format == 3){
                            value = $this.t(attachmentTemplate,dataContext.id,row);
                        }
                        // if(dataContext.format == 1){
                        //     value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.livrable_file_attachment,row);
                        // }
                        return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                    }
                    return '<div data-id="'+dataContext.id+'"><a id="upload-file-attachment" class="download-attachment" href="#"</a><a id="upload-file-url" style="margin-left: 0px" class="url-attachment" href="#"></a></div>';
                    // return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                livrableComment : function(row, cell, value, columnDef, dataContext){
                    return '<a class ="class_'+dataContext.id+'"data-profile = "'+ status +'" data-id="'+dataContext.id+'" href="#" class="liv_img_comment" onclick="liveCommentCallback('+dataContext.id+');" ><img style="width: 20px; height: 20px; margin: 5px" src="/img/extjs/icon-text-larger.png"></a>';
                },
                livrableDrag : function(row, cell, value, columnDef, dataContext){
                    if(!dataContext.livrable_file_attachment){
                        // value =  '<div data-id="'+dataContext.id+'" draggable="true">' + $this.t(attachmentDragTemplate,dataContext.id,row) + '</div>';
                        if(listId[dataContext.id] && dataContext.id !== undefined){

                        } else {
                            listId[dataContext.id] = dataContext.id;
                        }
                        return '<div data-id="'+dataContext.id+'" id="uploaderDocument'+dataContext.id+'" class="wd-input wd-calendar" style=""></div>';
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                percentValues : function(row, cell, value, columnDef, dataContext){
                    var val = number_format(value, 2, ',', ' ');
                    var _html = '<div style="position: relative; text-align: center"><div style="position: absolute; width: '+value+'%; height: 100%; background-color: rgb(77, 255, 130);"></div><span style="position: relative">'+val+' %</span></div>';
                    return _html;
                },
                livrableIcon : function(row, cell, value, columnDef, dataContext){
                    var plcid = dataContext.project_livrable_category_id;
                    var format = dataContext.format;
                    if(dataContext.livrable_file_attachment && dataContext.livrable_file_attachment != ''){
                        if(format == 2){
                            if(plcid && plcid !== undefined && livrableIcon[plcid] && livrableIcon[plcid] !== undefined ){
                                return '<a href="/project_livrables/attachement/'+dataContext.id+'?type=download"><img style="width: 24px; height: 24px; margin: 5px" src="/img/new-icon/project_document/' + livrableIcon[plcid] + '"></a>';
                            } else {
                                return '<a href="/project_livrables/attachement/'+dataContext.id+'?type=download"><img style="width: 24px; height: 24px; margin: 5px" src="/img/new-icon/project_document/z0g.svg"></a>';
                            }
                        } else if (format == 1) {
                            return '<a target="_blank" href="https://'+dataContext.livrable_file_attachment+'"><img style="width: 24px; height: 24px; margin: 5px" src="/img/extjs/icon-url.png"></a>';
                        } else if(format == 3){
                            return '<a href="#" class="local-file" data="'+dataContext.livrable_file_attachment+'"><img style="width: 24px; height: 24px; margin: 5px" src="/img/extjs/icon-url.png"></a>';
                        }
                    }
                    return '<img style="width: 24px; height: 24px; margin: 5px" src="/img/extjs/upload-icon.png">';
                },
                UploadDate : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell, '<div style="text-align: center;">' + value + '</div>', columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,dataContext.project_id,
                    $this.selectMaps.project_livrable_category_id[dataContext.project_livrable_category_id]),
                    columnDef, dataContext);
                }
            });
            $.extend(Slick.Editors,{
                livrableAttachment : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
                    // this.input = $("<a href='#' id='action-attach-url'></a><div class='browse'></div>")
                    this.input = $('<div><a class="download-attachment" href="#"</a><a style="margin-left: 45px" class="url-attachment" href="#"></a></div>')
                    .appendTo(args.container).attr('rel','no-history');
                    // $('#UploadAttachment').on('change', function(){
                    //     var form = $("#form_dialog_attachement_or_url");
                    //     form.find('input[name="data[Upload][id]"]').val(args.item.id);
                    //     console.log(form);
                    //     // form.submit();
                    // });
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
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_livrable_category_id : {defaulValue : ''},
                project_livrable_status_id : {defaulValue : ''},
                livrable_progression : {defaulValue : 0},
                livrable_responsible : {defaulValue : ''},
                livrable_date_delivery : {defaulValue : ''},
                livrable_date_delivery_planed : {defaulValue : ''},
                livrable_file_attachment : {defaulValue : '' , required : ['id']},
                format : {defaulValue : ''},
                version : {defaulValue : ''},
                actor_list : {defaulValue : []},
                name : {defaulValue : '', allowEmpty : false},
                weight : {defaulValue : 0 }
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.onBeforeEdit = function(args){
                if(args.column.field == 'livrable_file_attachment' && (!args.item || args.item['livrable_file_attachment'] || !args.item['id'])){
                    return false;
                }
                return true;
            }

            $this.onCellChange = function(args){
                var columns = args.grid.getColumns(),
                    col, cell = args.cell;
                do {
                    cell++;
                    if( columns.length == cell )break;
                    col = columns[cell];
                } while (typeof col.editor == 'undefined');

                if( cell < columns.length ){
                    args.grid.gotoCell(args.row, cell, true);
                } else {
                    //end of row
                    try {
                        args.grid.gotoCell(args.row + 1, 0);
                    } catch(ex) {}
                }
            }

            dataGrid = $this.init($('#project_container'),data,columns, {
                frozenColumn: 2
            });

            dataGrid.setSortColumns('weight' , true);

            dataGrid.setSelectionModel(new Slick.RowSelectionModel());
            $('.row-number').parent().addClass('row-number-custom');
            var moveRowsPlugin = new Slick.RowMoveManager({
                cancelEditOnDrag: true
            });
            moveRowsPlugin.onBeforeMoveRows.subscribe(function (e, data) {
                for (var i = 0; i < data.rows.length; i++) {
                        // no point in moving before or after itself
                    if (data.rows[i] == data.insertBefore || data.rows[i] == data.insertBefore - 1) {
                        e.stopPropagation();
                        return false;
                    }
                }
                return true;
            });
            $(dataGrid.getHeaderRow()).delegate(":input", "change keyup", function (e) {
                var text = $(this).val();
                if( text != '' ){
                    $(this).parent().css('border', 'solid 2px orange');
                } else {
                    $(this).parent().css('border', 'none');
                }
            });
            //fire after row move completed
            moveRowsPlugin.onMoveRows.subscribe(function (e, args) {
                var extractedRows = [], left, right;
                var rows = args.rows;
                var insertBefore = args.insertBefore;
                left = data.slice(0, insertBefore);
                right = data.slice(insertBefore, data.length);
                rows.sort(function(a,b) { return a-b; });
                for (var i = 0; i < rows.length; i++) {
                    extractedRows.push(data[rows[i]]);
                }
                rows.reverse();
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    if (row < insertBefore) {
                        left.splice(row, 1);
                    } else {
                        right.splice(row - insertBefore, 1);
                    }
                }
                data = left.concat(extractedRows.concat(right));

                var selectedRows = [];
                for (var i = 0; i < rows.length; i++)
                    selectedRows.push(left.length + i);

                //update no.
                var orders = { data : {} };
                for(var i = 0; i < data.length; i++){
                    data[i]['no.'] = (i+1);
                    data[i].weight = (i+1);
                    orders.data[data[i].id] = (i+1);
                }
                //ajax call
                $.ajax({
                    url : '<?php echo $html->url('/project_livrables/order/' . $projectName['Project']['id']) ?>',
                    type : 'POST',
                    data : orders,
                    success : function(){
                    },
                    error: function(){
                        location.reload();
                    }
                });
                dataGrid.resetActiveCell();
                var dataView = dataGrid.getDataView();
                dataView.beginUpdate();
                //if set data via grid.setData(), the DataView will get removed
                //to prevent this, use DataView.setItems()
                dataView.setItems(data);
                //dataView.setFilter(filter);
                //updateFilter();
                dataView.endUpdate();
                // dataGrid.getDataView.setData(data);
                dataGrid.setSelectedRows(selectedRows);
                dataGrid.render();
            });

            dataGrid.registerPlugin(moveRowsPlugin);
            dataGrid.onDragInit.subscribe(function (e, dd) {
                // prevent the grid from cancelling drag'n'drop by default
                e.stopImmediatePropagation();
            });
            // add new colum grid
            //ControlGrid = $this.init($('#project_container'),data,columns);
            addNewDeliverablesButton = function(){
                dataGrid.gotoCell(data.length, 3, true);
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
            $("#upload-file-url").live('click',function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            $("#upload-file-attachment").live('click', function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            //
            $('.update_drag_class').live('click', function(){
                $(this).val('');
                return false;
            });
            $('.update_drag_class').live('change', function(){
                var form = $(this).parent().parent();
                var _id = $(form).parent().attr('data-id');
                form.find('input[name="data[Upload][id]"]').val(_id);
                form.submit();
            });
            
            window.addEventListener("dragover",function(e){
                e = e || event;
                if(!$(e.target).hasClass('update_drag_class')){
                    e.preventDefault();
                }
            },false);
            window.addEventListener("drop",function(e){
                if(!$(e.target).hasClass('plupload_droptext') && !$(e.target).hasClass('plupload_filelist')){
                    e.preventDefault();
                } else {
                    if($(e.target).hasClass('plupload_droptext')){
                        var element = $(e.target).parent().next().find('a.plupload_start');
                    } else {
                        var element = $(e.target).next().find('a.plupload_start');
                    }
                    setTimeout(function(){
                        $(element).trigger('click');
                    }, 200);
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            },false);
            /**
             * Multiple Upload
             */
            setTimeout(function(){
                for (var i = 0; i < listId.length; i++) {
                    if(listId[i] !== undefined){
                        var uploader = $("#uploaderDocument" + listId[i]).pluploadQueue({
                            runtimes : 'html5, html4',
                            url : "/project_livrables/uploads/" + <?php echo json_encode($projectName['Project']['id']) ?> + '/' + listId[i],
                            chunk_size : '10mb',
                            rename : true,
                            dragdrop: true,
                            filters : {
                                max_file_size : '10mb',
                                mime_types: [
                                    {title : "Files", extensions : "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg,xlsm"}
                                ]
                            },
                            init: {
                                PostInit: function(up) {
                                    up.project_id = _project_id;
                                    up.linkedAction = '/project_livrables/attachment/';
                                    if(projectFiles1 && Object.keys(projectFiles1).length > 0){
                                        up.auditFiles = projectFiles1;
                                        var tmpHtml = '';
                                        var display_none = '';
                                        if(showAllFieldYourform == 0){
                                            display_none = 'display: none';
                                        }
                                        $.each(projectFiles1, function(ind, val){
                                            var hrefDownload = '/projects/attachment/upload_documents_1'+'/'+_project_id+'/'+val.id+'/download/';
                                            var hrefDelete = '/projects/attachment/upload_documents_1'+'/'+_project_id+'/'+val.id+'/delete/';
                                            tmpHtml +=
                                            '<li id="' + val.id + '" class="plupload_done">' +
                                                '<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                                                '<div class="plupload_file_action_modify">' +
                                                '<a class="download-attachment" href="' +hrefDownload+ '" rels=' + val.id + '>Download</a>' +
                                                '<a class="delete-attachment" style="'+display_none+'" href="' +hrefDelete+ '" rels=' + val.id + '>Delete</a></div>' +
                                                '<div class="plupload_file_action"><a href="#" style="display: block;"></a></div>' +
                                                '<div class="plupload_file_status">' + 100 + '%</div>' +
                                                '<div class="plupload_file_size">' + plupload.formatSize(val.size) + '</div>' +
                                                '<div class="plupload_clearer">&nbsp;</div>' +
                                            '</li>';
                                        });
                                        $('#uploaderDocument1_filelist').html(tmpHtml);
                                    }
                                }
                            }
                        });
                    }
                }
            }, 2000);

            $('.local-file').live('click', function(){
                var createDialog1 = function(){
                    $('#template-local-file').dialog({
                        position    :'center',
                        autoOpen    : false,
                        autoHeight  : true,
                        modal       : true,
                        width       : 500,
                        minHeight   : 50,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog = $.noop;
                }
                createDialog1();
                var titlePopup = <?php echo json_encode(__('Local directory', true))?>;
                $("#template-local-file").dialog('option',{title:titlePopup}).dialog('open');
                var url = $(this).attr('data');
                $("#template-local-file").find('input').val(url);
                $('#local_directory_link').append('<a style="color: black; font-size: 13px;margin-left: 10px;line-height: 29px;" target="_blank" href="file:///'+url+'">'+url+'</a>')
            });
            $('#UploadAttachment').live('change', function(){
                var form = $("#form_dialog_attachement_or_url");
                var _id = $('.active.selected').find('div').attr('data-id');
                form.find('input[name="data[Upload][id]"]').val(_id);
                form.submit();
            });
            $("#ok_attach").click(function(){
                //self.input[0].remove();
                $('#action-attach-url').css('display', 'none');
                $('.browse').css('display', 'block');
                $("#dialog_attachement_or_url").dialog('close');
                var _id = $('.active.selected').find('div').attr('data-id');
                var form = $("#form_dialog_attachement_or_url");
                form.find('input[name="data[Upload][id]"]').val(_id);
                form.submit();
            });
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
                $('.item-toggle').removeClass('btn-disable');
                $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
                $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('#hidden_checkbox').click(function(){
                var checked = $('#hidden_checkbox').attr('checked');
                var url = window.location.pathname;
                checked = checked && checked == "checked" ? true : false;
                $.ajax({
                    url: '/project_livrables/saveHiddenColums/' + <?php echo json_encode($projectName['Project']['id']) ?>,
                    type: 'POST',
                    data: {
                        checked : checked,
                        url : url
                    },
                    dataType: 'json',
                    success: function(data) {
                        location.reload();
                    }
                });
            });
            $("#gs-attach").click(function(){
                $(this).removeClass('gs-attach-remove');
                $('#gs-url').removeClass('gs-url-add');
                $('.item-toggle').addClass('btn-disable');
                $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
                $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('.row-number').parent().addClass('row-number-custom');

        });
        function setupScroll(){
            $("#scrollTopAbsenceContent").width($(".grid-canvas-right:first").width()+50);
            $("#scrollTopAbsence").width($(".slick-viewport-right:first").width());
        }
        setTimeout(function(){
            setupScroll();
        }, 2500);
        $("#scrollTopAbsence").scroll(function () {
            $(".slick-viewport-right:first").scrollLeft($("#scrollTopAbsence").scrollLeft());
        });
        $(".slick-viewport-right:first").scroll(function () {
            $("#scrollTopAbsence").scrollLeft($(".slick-viewport-right:first").scrollLeft());
        });
        history_reset = function(){
            var check = false;
            $('.multiselect-filter').each(function(val, ind){
                var text = '';
                if($(ind).find('input').length != 0){
                    text = $(ind).find('input').val();
                } else {
                    text = $(ind).find('span').html();
                    if( text == "<?php __('-- Any --');?>" || text == '-- Any --'){
                        text = '';
                    }
                }
                if( text != '' ){
                    $(ind).css('border', 'solid 2px orange');
                    check = true;
                } else {
                    $(ind).css('border', 'none');
                }
            });
            if(!check){
                $('#reset-filter').addClass('hidden');
            } else {
                $('#reset-filter').removeClass('hidden');
            }
        }
        resetFilter = function(){
            // HistoryFilter.stask = '{}';
            // HistoryFilter.send();
            // $('.multiselect-filter').each(function(val, ind){
                // if($(ind).find('input').length != 0){
                    // $(ind).find('input').val('');
                // } else {
                    // $(ind).find('span').html("<?php __('-- Any --');?>");
                // }
                // $(ind).css('border', 'none');
                // $('#reset-filter').addClass('hidden');
            // });
            // setTimeout(function(){
                // location.reload();
            // }, 500);
			$.ajax({
				url : '/employees/history_filter',
				type: 'POST',
				data: {
					data: {
						path: HistoryFilter.here,						
					}
				},
				success : function(respons){
					var _data =  $.parseJSON(respons);
					$.each(_data, function( _index, _val){
						if( _index.indexOf('Resize') == -1){
							 _data[_index]='';
						}
						
					});
					HistoryFilter.stask = _data;
					HistoryFilter.send();
					setTimeout(function(){
						location.reload();
					}, 500);
				}
			});
        }
    })(jQuery);
</script>
