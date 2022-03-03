<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'preview/project_risk',
));
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
    'autosize.min'
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
    .icon-color{
        height: 14px;
        width: 14px;
        border-radius: 50%;
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px
    }
    .task_red{
        background-image: url(/img/extjs/icon-triangle.png);
        background-repeat: no-repeat;
        padding-left: 20px;
        cursor: pointer;
    }
    .download-attachment{
        background-image: url("/img/extjs/icon-task-folder.png");
    }
    .muti_text{
        background-image: url("/img/extjs/icon-text.png");
        background-repeat: no-repeat;
        display: block;
        cursor: pointer;
        width: 25px;
        margin-top: 5px;
        margin-left: 15px;
    }
    #comment-ct{
        max-height: 500px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .comment-ct{
        min-height: 50px;
    }
    .comment-btn{
        float: left;
        margin-left: 45px;
    }
    .comment-btn textarea{
        width: 300px;
        margin-left: -4px;
        margin-top: 20px;
        padding: 8px;
        line-height:1.5;
        font:13px Tahoma, cursive;
        max-height: 400px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .submit-btn-msg{
        width: 70px;
        height: 50px;
        border: none;
        vertical-align: top;
        color: #fff;
        text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.3);
        position: relative;
        margin-top: 20px;
        font-size: 20px;
        background-color: #56aaff !important;
    }
    .submit-btn-msg img{
        height: 40px;
    }
    .submit-btn-msg:hover{
        cursor: pointer;
    }
    .comment{
        min-height: 70px;
        max-width: 320px;
        background-color: #549ac1;
        border-bottom: 5px solid #fff;
        width: auto;
        float: left;
        padding-right: 30px;
        border-radius: 10px;
        margin-right: 40px;
    }
    .left-comment{
        width: 50px;
        float: left;
        clear: both;
        overflow: hidden;
    }
    .right-comment{
        padding-top: 5px;
        max-width: 684px;
        margin-left: 10px;
        padding-bottom: 5px;
    }
    .right-comment b{
        color: #578cca;
    }
    .right-comment span {
        /*margin-left: 10px;*/
    }
    .my-comment{
        background-color: #7ab3d1;
        color: #fff;
        min-height: 50px;
        border-bottom: 5px solid #fff;
        margin-right: 50px;
        width: auto;
        clear: both;
        float: right;
        padding-left: 30px;
        border-radius: 10px;
        margin-left: 40px;
        padding: 5px;
    }
    .my-date{
        float: right;
        margin-right: 30px;
        display: block;
        color: #000;
    }
    .my-content{
        float: right;
        clear: both;
        margin-right: 30px;
    }
    .right-avatar{
        float: right;
        margin-top: -70px;
        clear: both;
    }
    .right-avatar img{
        height: 40px;
        width: 40px;
        border-radius: 30px;
        margin-right: 10px;
        margin-top: 5px;
    }
    .avatar{
        border-radius: 50%;
        width: 40px;
        height: 40px;
    }
    .show-hide img{
        height: 30px;
    }
    .show-hide img:hover{
        cursor: pointer;
    }
    .hidden-div{
        display: none;
    }
    .message-new-ct{
        float: right;
        background-color: red;
        color: #fff;
        border-radius: 50%;
        width:20px;
        text-align: center;
        display: block;
        height: 20px;
        margin-top: 5px;
        margin-right: 5px;
        line-height: 20px;
    }
    .delete-attachment, .download-attachment{
        margin: 0;
    }
    .muti-color{
        width: 100px !important;
        height: auto !important;
    }
    #wd-container-footer{
        display: none;
    }
    .slick-viewport-right{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
	.wd-tab{
		max-width: none;
	}
</style>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="not-attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <i style="margin-left: -2px" class="download-attachment" href=""><?php echo __('Download', true); ?></i>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => 'project_issues', 'action' => 'upload')
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
<div id="commentsPopup" class="buttons" style="display: none;">
    <div class="modal-body">
    </div>
    <div class="comment-btn">
        <button onclick="saveCommentPj()" class="submit-btn-msg" type="button">
            <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png');?>" alt="">
        </button>
        <textarea class="textarea-ct" name="name" rows="2" cols="40"></textarea>
    </div>
</div>
<!-- dialog_attachement_or_url.end -->
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_issues', 'action' => 'export', $projectName['Project']['id'])));
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
                    <a href="javascript:void(0);" class="btn export-excel-icon-all" id="export-submit" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                    <a href="javascript:void(0);" onclick="expandScreen();" class="btn btn-expand hide-on-mobile" id="expand"></a>
                     <a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapseScreen();" title="Collapse Tasks Screen" style="display: none;"></a>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="group-content">
                    <div id="pi-log" class="kpi-log">
                        <ul>
                            <?php if($checkIsChang === 'true'):
                                $normal = array("á", "à", "ả", "ã", "ạ", "ắ", "ằ", "ẳ", "ẵ", "ặ", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ố", "ồ", "ổ", "ỗ", "ộ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "Á", "À", "Ả", "Ã", "Ạ", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ỏ", "Õ", "Ọ", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "ă", "â", "ê", "ô", "ơ", "ư", "đ", "Ă", "Â", "Ê", "Ô", "Ò", "Ư", "Đ");
                                 $flat = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "a", "a", "e", "o", "o", "u", "d", "A", "A", "E", "O", "O", "U", "D");

                            ?>
                            <li class="new-log" data-log-id="">
                                <img class="log-avatar" src="<?php echo $this->UserFile->avatar($employee_info['Employee']['id']) ?>">
                                <div class="log-body">
                                    <h4 class="log-author"><?php echo str_replace( $normal, $flat, $employee_info['Employee']['fullname'] ); ?></h4>
                                    <em class="log-time"></em>
                                    <textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" onchange="updateLog.call(this, 'ProjectIssue')"></textarea>
                                </div>
                            </li>
                            <?php endif ?>
                            <?php
                                if(!empty($logSystems)){
                                    //$checkIsChang
                                    foreach($logSystems as $logSystem){
                                        $linkAvatar = $linkAvatar = $this->UserFile->avatar($logSystem['employee_id']);
                                        $name = str_replace( $normal, $flat, $logSystem['name']);
                            ?>
                            <li id="risk-<?php echo $logSystem['id'] ?>" class="flash-field">
                                <div class="risk-log-content">
                                    <p class="circle-name"><img title = "<?php echo $name ?>" src="<?php echo $linkAvatar ?>"></p>
                                    <p class="log-author" style="display: none"><?php echo $name ?></p>
                                    <span class="log-time"><?php echo date('d M Y', $logSystem['created']) ?></span>
                                    <a href="javascript:void(0)" class="log-field-edit"><img src="/img/new-icon/edit-task.png"></a>
                                    <textarea data-log-id="<?php echo $logSystem['id'] ?>" class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" <?php echo ((!$canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> onchange="updateLog.call(this, 'ProjectIssue')"><?php echo $logSystem['description'] ?></textarea>
                                </div>
                                <?php if(!(!$canModified && !$_isProfile)|| ($_isProfile && $_canWrite)){ ?>
                                <a href="javascript:void(0);" onclick="show_comment_popup(this);" class="log-addnew"></a>
                                <div class="template_logs" style="display: none;">
                                    <div class="add-comment">
                                        <textarea data-log-id= "" class="add-comment-text not_save_history" id="risk-comment-text" name="risk-comment-text" rows="5" onfocus="autosize(this)" onblur="autosize.destroy(this)"  onchange="autosize(this)" placeholder="<?php __('Your message'); ?>"></textarea>
                                        <p class="submit-row">
                                            <a class="button add-comment-submit" href="javascript:void(0);" onclick="sent_comment(this,'ProjectIssue');"><?php __('Enregistrer'); ?></a>
                                        </p>
                                    </div>
                                </div>
                                <?php } ?>
                            </li>
                            <?php
                                    }
                                }else{
                                    ?>
                                    <li id="risk" class="flash-field field-empty">
                                        <div class="risk-log-content">
                                            <span><?php __('Empty'); ?></span>
                                        </div>
                                        <?php if(!(!$canModified && !$_isProfile)|| ($_isProfile && $_canWrite)){ ?>
                                        <a href="javascript:void(0);" onclick="show_comment_popup(this);" class="log-addnew"></a>
                                        <div class="template_logs" style="display: none;">
                                            <div class="add-comment">
                                                <textarea data-log-id= "" class="add-comment-text not_save_history" id="risk-comment-text" name="risk-comment-text" rows="5" onfocus="autosize(this)" onblur="autosize.destroy(this)"  onchange="autosize(this)" placeholder="<?php __('Your message'); ?>"></textarea>
                                                <p class="submit-row">
                                                    <a class="button add-comment-submit" href="javascript:void(0);" onclick="sent_comment(this,'ProjectIssue');"><?php __('Enregistrer'); ?></a>
                                                </p>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </li>

                                    <?php 
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="popup-add">
                    <a class="add-new-item" href="javascript:void(0);" onclick="addNewIssueButton();"><img title="Add an item" src="/img/new-icon/add.png"></a>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:450px;">
                    
                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden;">

                </div>
            </div>

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

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        'cssClass' => 'slick-cell-move-handler'
    ),
    // array(
    //     'id' => 'project_issue_color_id',
    //     'field' => 'project_issue_color_id',
    //     'name' => __('Blocking', true),
    //     'width' => 70,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'editor' => 'Slick.Editors.selectBachground',
    //     'formatter' => 'Slick.Formatters.selectBachground'
    // ),
    array(
        'id' => 'project_issue_problem',
        'field' => 'project_issue_problem',
        'name' => __('Issue', true),
        'width' => 250,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'project_issue_severity_id',
        'field' => 'project_issue_severity_id',
        'name' => __('Severity', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'formatter' => 'Slick.Formatters.iconColor'
    ),
    array(
        'id' => 'project_issue_status_id',
        'field' => 'project_issue_status_id',
        'name' => __('Status', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    // array(
    //     'id' => 'issue_assign_to',
    //     'field' => 'issue_assign_to',
    //     'name' => __('Assign to', true),
    //     'width' => 150,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'editor' => 'Slick.Editors.mselectBox'
    // ),
    array(
        'id' => 'issue_action_related',
        'field' => 'issue_action_related',
        'name' => __('Actions related', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'formatter' => 'Slick.Formatters.actionRelative'
    ),
    // array(
    //     'id' => 'delivery_date',
    //     'field' => 'delivery_date',
    //     'name' => __('Delivery Date', true),
    //     'width' => 100,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'editor' => 'Slick.Editors.datePicker',
    //     'formatter' => 'Slick.Formatters.imageRed',
    //     'validator' => 'DateValidate.startDate'
    // ),
    // array(
    //     'id' => 'date_open',
    //     'field' => 'date_open',
    //     'name' => __('Created Date', true),
    //     'width' => 100,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'editor' => 'Slick.Editors.datePicker',
    //     'validator' => 'DateValidate.startDate'
    // ),
    array(
        'id' => 'date_issue_close',
        'field' => 'date_issue_close',
        'name' => __('Date closing', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.startDate'
    ),
    // array(
    //     'id' => 'file_attachement',
    //     'field' => 'file_attachement',
    //     'name' => '',
    //     'width' => 100,
    //     'noFilter' => 1,
    //     'sortable' => false,
    //     'resizable' => false,
    //     'editor' => 'Slick.Editors.Attachement',
    //     'formatter' => 'Slick.Formatters.Attachement'
    // ),
    // array(
    //     'id' => 'text',
    //     'field' => 'text',
    //     'name' => '',
    //     'width' => 60,
    //     'sortable' => true,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.MutiText'
    // ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __(' ', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
	
foreach($columns as $key => $column){
	if(!empty($loadFilter) && !empty($loadFilter[$column['field']. '.Resize'])){
		$columns[$key]['width'] = intval($loadFilter[$column['field']. '.Resize']);
	}
}
$i = 1;
$dataView = array();
$selectMaps = array(
    'project_issue_severity_id' => $issueSeverities,
    'project_issue_status_id' => $issueStatus,
    'issue_assign_to' => $listEmployeeAndProfit,
);

foreach ($projectIssues as $projectIssue) {
    $data = array(
        'id' => $projectIssue['ProjectIssue']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );

    $data['project_issue_severity_id'] = $projectIssue['ProjectIssue']['project_issue_severity_id'];
    $data['project_issue_status_id'] = $projectIssue['ProjectIssue']['project_issue_status_id'];
    $data['project_issue_color_id'] = $projectIssue['ProjectIssue']['project_issue_color_id'];
    $data['project_issue_problem'] = $projectIssue['ProjectIssue']['project_issue_problem'];
    $data['issue_action_related'] = $projectIssue['ProjectIssue']['issue_action_related'];
    $data['delivery_date'] = $str_utility->convertToVNDate($projectIssue['ProjectIssue']['delivery_date']);
    $data['date_issue_close'] = $str_utility->convertToVNDate($projectIssue['ProjectIssue']['date_issue_close']);
    $data['date_open'] = $str_utility->convertToVNDate($projectIssue['ProjectIssue']['date_open']);
    $data['file_attachement'] = (string) $projectIssue['ProjectIssue']['file_attachement'];
    $data['format'] = (string) $projectIssue['ProjectIssue']['format'];
    $data['weight'] = $projectIssue['ProjectIssue']['weight'];

    $data['issue_assign_to'] = array();
    if(!empty($_issueEmployeeRefer[$projectIssue['ProjectIssue']['id']])){
        $v = $_issueEmployeeRefer[$projectIssue['ProjectIssue']['id']];
        foreach ($v as $key => $value) {
            $data['issue_assign_to'][$key] = $value['is_profit_center'] . '-' . $value['reference_id'];
        }
    }

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
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true)
);
$listAvartar = array();
$listIdEm = array_keys($listEmployee);
foreach ($listIdEm as $_id) {
    $_id = str_replace('0-', '', $_id);
    $link = $this->UserFile->avatar($_id, "small");
    $listAvartar[$_id] = $link;
}
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
function get_grid_option(){
    var _option ={
        frozenColumn: '',
        // enableAddRow: false,            
        // showHeaderRow: false,
        rowHeight: 40,
        // forceFitColumns: true,
        topPanelHeight: 40,
        headerRowHeight: 40,
    };

    if( $(window).width() > 992 ){
        return _option;
    }
    else{
        _option.frozenColumn = '';
        _option.forceFitColumns = false;
        return _option;
    }
}
var wdTable = $('.wd-table');
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
    var issueSeverities = <?php echo json_encode($issueSeverities) ?>;
    var colorSeverities = <?php echo json_encode($colorSeverities) ?>;
    var colorDefault = <?php echo json_encode($colorDefault) ?>;
    var listColor = <?php echo json_encode($listColor) ?>;
    var issueStatus = <?php echo json_encode($issueStatus) ?>;
    var listAvartar = <?php echo json_encode($listAvartar) ?>;
    var employee_id = <?php echo json_encode($employee_id); ?>;
    var DateValidate = {},dataGrid,IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);
    };
    var projectName = <?php echo json_encode($projectName['Project']); ?>;
    (function($){
        $(function(){
            var $this = SlickGridCustom;

            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = dataGrid.getDataItem(row);
                if(data && confirm($this.t('Are you sure you want to delete attachement : %s'
                , data['file_attachement']))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['file_attachement'] = '';
                    dataGrid.updateRow(row);
                }
                return false;
            });

            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)); ?>;
            // For validate date
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

            DateValidate.startDate = function(value){
                value = getTime(value);
                if(projectName['start_date'] == ''){
                    _valid = true;
                    _message = '';
                    //_message = $this.t('Start-Date or End-Date of Project are missing. Please input these data before full-field this date-time field.');
                } else {
                    //_valid = value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']);
                    //_message = $this.t('Date closing must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date']);
                    _valid = value >= getTime(projectName['start_date']);
                    _message = $this.t('Date closing must larger %1$s' ,projectName['start_date']);
                }
                return {
                    valid : _valid,
                    message : _message
                };
            }

            var actionTemplate =  $('#action-template').html(),
                attachmentTemplate =  $('#attachment-template').html(),
                notAttachmentTemplate =  $('#not-attachment-template').html(),
                attachmentURLTemplate =  $('#attachment-template-url').html();

            $.extend(Slick.Formatters,{
                Attachement : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        value = $this.t(attachmentTemplate,dataContext.id,row);
                    } else {
                        value = $this.t(notAttachmentTemplate,dataContext.id,row);
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.project_issue_problem), columnDef, dataContext);
                },
                iconColor: function(row, cell, value, columnDef, dataContext){
                    var color = colorSeverities && colorSeverities[value] ? colorSeverities[value] : '#004380';
                    return '<i class="icon-color" style="background-color:' + color + '">&nbsp</i><span style="margin-left: 5px">' + issueSeverities[value] + '</span>';
                },
                selectBachground: function(row, cell, value, columnDef, dataContext){
                    if(value != 0 && value != null){
                        return '<div style="width: 20px; height: 20px; background-color: '+listColor[value]+'; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
                    }
                    return '<div style="width: 20px; height: 20px; background-color: '+colorDefault+'; padding-left: 0px; margin:0 auto;margin-top : 4px;"></div>';
                },
                imageRed: function(row, cell, value, columnDef, dataContext){
                    if(value != ''){
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth()+1; //January is 0!
                        var yyyy = today.getFullYear();
                        dd = dd < 10 ? '0' + dd : dd;
                        mm = mm < 10 ? '0' + mm : mm;

                        curDate = new Date(yyyy + '-' + mm + '-' + dd).getTime();
                        var sDate = value.split('-');
                        sDate = new Date(sDate[2] + '-' + sDate[1] + '-' + sDate[0]).getTime();
                        if((curDate > sDate) && (issueStatus[dataContext['project_issue_status_id']] != 'CLOS')){
                            return '<div style="text-align: center;"><span class="task_red">' + value + '</span></div>';
                        }
                    }
                    return '<div style="text-align: center;"><span>' + value + '</span></div>';
                },
                MutiText: function(row, cell, value, columnDef, dataContext){
                    return '<div style="text-align: center"><i class="muti_text" onclick="callMutitext('+dataContext.id+')">&nbsp</i></div>';
                },
                actionRelative: function(row, cell, value, columnDef, dataContext){
                    if(dataContext){
                        var _html = '';
                        var avatar = src = '';                            
                        if(issue_action_update && issue_action_update[dataContext['id']]){
                            employee_id = issue_action_update[dataContext['id']]['employee_id'];
                            employee_name = employee_update[employee_id]['first_name'] +' '+employee_update[employee_id]['last_name'];
                            if(is_avatar[issue_action_update[dataContext['id']]['employee_id']]){
                                var src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', employee_id);
                                avatar = '<span class="circle-name" title ="'+ employee_name +'" ><img src ="'+ src + '" /></span>';
                            }else{
                                first_name  =  employee_update[employee_id]['first_name'].substr(0,1);
                                last_name  =  employee_update[employee_id]['last_name'].substr(0,1);
                                avatar = '<span class="circle-name" title ="'+ employee_name +'">'+ first_name + last_name +'</span>';
                            }
                        }
                        return avatar + value;
                    }
                }
            });

            $.extend(Slick.Editors,{
                Attachement : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
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

                    createDialog();
                    var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                    $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');

                    $(".cancel").live('click',function(){
                        $("#dialog_attachement_or_url").dialog('close');
                    });
                    this.input = $("<div style='overflow: hidden;' class='img-to-right'><i style='margin-left: -2px' class='download-attachment' href=''><?php echo __('Download', true); ?></i></div>")
                    .appendTo(args.container);
                    // .attr('rel','no-history').addClass('editor-text');
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
                selectBachground: function(args){
                    var $options,hasChange = false,isCreated = false;
                    var defaultValue = [];
                    var scope = this;
                    var oldColor_id = args.item.project_issue_color_id;

                    setColor = function(color_id){
                        var rowId = args.item.id;
                        var cell = args.container[0];
                        // $('.multiSelectOptions').hide();
                        $(cell).find('div').css('background-color', listColor[color_id]);
                        args.item['project_issue_color_id'] = color_id;
                        Slick.Formatters.selectBachground(null,null,color_id, args.column ,args.item);
                        if(oldColor_id != color_id){
                            hasChange = true;
                        } else {
                            hasChange = false;
                        }
                    }
                    var preload = function(){
                        var _html = '';
                        _html += '<form>';
                        $.each(listColor, function(ind, val){
                            if(ind == oldColor_id){
                                _html += '<input type="radio" onClick="setColor('+ind+')" name="color" checked style="float: left" rel="no-history"/><div style="margin-left: 20px; height: 22px; background-color: '+val+'" >&nbsp;</div>';
                            } else {
                            _html += '<input type="radio" onClick="setColor('+ind+')" name="color" style="float: left" rel="no-history"/><div style="margin-left: 20px; height: 22px; background-color: '+val+'" >&nbsp;</div>';
                            }
                        });
                        _html += '</form>'
                        $options.html(_html);
                        scope.input.html('');
                        scope.setValue(defaultValue);
                        scope.input.select();
                    }
                    $.extend(this, new BaseSlickEditor(args));

                    $options = $('<div class="multiSelectOptions muti-color" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px;"></div>').appendTo('body');
                    var hideOption = function(){
                        scope.input.removeClass('active').removeClass('hover');
                        $options.css({
                            visibility:'hidden',
                            display : 'none'
                        });
                    }
                    this.input = $('<a href="javascript:void(0);" class="multiSelect"></a>')
                    .appendTo(args.container)
                    .hover( function() {
                        scope.input.addClass('hover');
                    }, function() {
                        scope.input.removeClass('hover');
                    })
                    .click( function(e) {
                        // Show/hide on click
                        if(scope.input.hasClass('active')) {
                            hideOption();
                        } else {
                            var offset = scope.input.addClass('active').offset();
                            $options.css({
                                top:  offset.top + scope.input.outerHeight() + 'px',
                                left: offset.left + 'px',
                                visibility:'visible',
                                display : 'block'
                            });
                            $options.find('input[type=text]').focus().select();
                            if(scope.input.width() < 320){
                                $options.width(100);
                            }
                        }
                        e.stopPropagation();
                        return false;
                    });

                    $(document).click( function(event) {
                        if(!($(event.target).parents().andSelf().is('.multiSelectOptions'))){
                            hideOption();
                        }
                    });

                    var destroy = this.destroy;
                    this.destroy = function () {
                        $options.remove();
                        destroy.call(this, $.makeArray(arguments));
                    };

                    this.getValue = function (val) {
                        if(this.input.html() == 'Loading ...'){
                            if(val ==true){
                                return true;
                            }
                            return '';
                        }
                        return this.input.html().split(',');
                    };

                    this.setValue = function (val) {
                        if(val === true){
                            val = 'Loading ...';
                        }else{
                            val = Slick.Formatters.selectBachground(null,null,val, args.column ,args.item);
                        }
                        this.input.html(val);
                    };

                    this.loadValue = function (item) {
                        defaultValue = item[args.column.field] || "";
                    };

                    this.serializeValue = function () {
                        if(!isCreated){
                            this.loadValue(args.item);
                            preload();
                        }
                        return scope.getValue();
                    };

                    var applyValue = this.applyValue;
                    this.applyValue = function (item, state) {
                        if($.isEmptyObject(item)){
                            applyValue.call(this, item , state);
                        }
                        $.extend(item ,args.item , true);
                    };

                    this.isValueChanged = function () {
                        return (hasChange == true);
                    };
                    this.focus();
                }
           });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_issue_severity_id : {defaulValue : ''},
                project_issue_status_id : {defaulValue : ''},
                issue_assign_to : {defaulValue : []},
                project_issue_problem : {defaulValue : '' , allowEmpty : false},
                issue_action_related : {defaulValue : ''},
                date_issue_close : {defaulValue : ''},
                delivery_date : {defaulValue : ''},
                date_open : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                weight : {defaulValue : 0 },
                project_issue_color_id : {defaulValue : ''},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            dataGrid = $this.init($('#project_container'),data,columns, get_grid_option());
            $this.onBeforeEdit = function(args){
                if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
                    return false;
                }
                return true;
            }
            // add new colum grid
            //ControlGrid = $this.init($('#project_container'),data,columns);
            addNewIssueButton = function(){
                dataGrid.gotoCell(data.length, 1, true);
            }
            $this.onCellChange = function(args){
                if(args.column.field == 'project_issue_status_id'){
                    if(issueStatus[args.item.project_issue_status_id] == 'CLOS'){
                        var date = new Date();
                        var d = (date.getDate() > 10 ? date.getDate() : '0' + date.getDate()) + '-' + ((date.getMonth() + 1) > 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1)) + '-' + date.getFullYear();
                        args.item['date_issue_close'] = d;
                    }
                }
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
                    url : '<?php echo $html->url('/project_issues/order/' . $projectName['Project']['id']) ?>',
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

        });

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

        /**
         *  Set time paris
         */
        setAndGetTimeOfParis = function(){
            //var _date = new Date().toLocaleString('en-US', {timeZone: 'Europe/Paris'}); // khong dung dc tren IE
            var _date = new Date(); // Lay Ngay Gio Thang Nam Hien Tai
            /**
             * Lay Ngay Gio Chuan Cua Quoc Te
             */
            var _day = _date.getUTCDate();
            var _month = _date.getUTCMonth() + 1;
            var _year = _date.getUTCFullYear();
            var _hours = _date.getUTCHours();
            var _minutes = _date.getUTCMinutes();
            var _seconds = _date.getUTCSeconds();
            var _miniSeconds = _date.getUTCMilliseconds();
            /**
             * Tinh gio cua nuoc Phap
             * Nuoc Phap nhanh hon 2 gio so voi gio Quoc te.
             */
            _hours = _hours + 2;
            if(_hours > 24){
                _day = _day + 1;
                if(_day > daysInMonth(_month, _year)){
                    _month = _month + 1;
                    if(_month > 12){
                        _year = _year + 1;
                    }
                }
            }
            _day = _day < 10 ? '0'+_day : _day;
            _month = _month < 10 ? '0'+_month : _month;
            return _hours + ':' + _minutes + ' ' + _day + '/' + _month + '/' + _year;
        };

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
			$('.multiselect-filter input').val('').trigger('change');
			$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
			dataGrid.setSortColumn();
			$('input[name="project_container.SortOrder"]').val('').trigger('change');
			$('input[name="project_container.SortColumn"]').val('').trigger('change');
        }
    })(jQuery);
    /**
     * Add log system of sale lead
     */
    var companyName = <?php echo json_encode($companyName);?>,
        company_id = <?php echo json_encode($company_id);?>,
        employeeLoginName = <?php echo json_encode($employeeLoginName);?>,
        is_avatar = <?php echo json_encode($is_avatar);?>,
        issue_action_update = <?php echo json_encode($issue_action_update);?>,
        employee_update = <?php echo json_encode($employee_update);?>,
        employeeLoginId = <?php echo json_encode($employeeLoginId);?>;
    function addLog(id){
        var nl = $(id).find('.new-log').toggle();
        if( nl.is(':visible') ){
            nl.find('.log-content').focus();
        }
    }
    function updateLog(model){
        var inp = $(this),
            li = $(this).closest('li');
        var value = $.trim(inp.val()),
            log_id = li.data('log-id');
        if( value ){
            // loading
            inp.prop('disabled', true);
            // save
            $.ajax({
                url: '<?php echo $html->url(array('controller' => 'project_amrs', 'action' => 'update_data_log')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    data: {
                        id: log_id,
                        company_id: company_id,
                        model: model,
                        model_id: projectName['id'],
                        name: li.find('.log-author').text(),
                        description: value,
                        employee_id: employeeLoginId,
                        update_by_employee: employeeLoginName
                    }
                },
                success: function(response) {
                    var data = response.LogSystem;
                    if( !log_id ){
                        var newLi = li.clone();
                        newLi.removeClass('new-log');
                        newLi.find('.log-content').prop('rowspan', 1);
                        newLi.find('.log-time').text(data.time);
                        newLi.data('log-id', data.id);
                        newLi.insertAfter(li);
                        // reset li
                        inp.val('').prop('disabled', false);
                        // reset new Li
                        inp = newLi.find('.log-content');
                        // hide
                        li.hide();
                    }
                },
                complete: function(){
                    // hide loading
                    // can change
                    inp.prop('disabled', false).css('color', '#3BBD43');
                }
            });
        }
    }
    function callMutitext(id){
        $('#commentsPopup').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 450,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
        $("#commentsPopup").dialog('option',{title:''}).dialog('open');
        $('.submit-btn-msg').attr('data-id', id);
        var popup = $("#commentsPopup");
        var body = popup.find(".modal-body");
        var _html = '';
        body.html(_html);
        $.ajax({
            url: '/project_issues/getComment',
            type: 'POST',
            data: {
                id: id,
                model: 'project_issues'
            },
            dataType: 'json',
            success: function(data) {
                if (data['comment']) {
                    $.each(data['comment'], function(ind, _data) {
                        var idEm = _data['employee_id']['id'],
                            name = _data['employee_id']['first_name'] + ' ' + _data['employee_id']['last_name'],
                            content = _data['content'].replace(/\n/g, "<br>"),
                            date = _data['created'];
                        var link = listAvartar[idEm];
                        if (employee_id == idEm) {
                            _html += '<div class="my-comment"><div class="my-date"><span>' + date + '</span></div><div class="my-content"><span>' + content + '</span></div></div><div class="right-avatar"><img class="avatar" src="'+link+'" alt="photo"></div>';
                        } else {
                            _html += '<div class="left-comment"><img class="avatar" src="' + link + '" alt="photo"></div><div class="comment"><div class="right-comment"><div><span>' + name + '</span><span class="date"> &nbsp' + date + '</span></div><div><span>' + content + '</span></div></div></div>';
                        }
                    });
                } else {
                    _html += '';
                }
                body.html(_html);
            }
        });
    }
    var saveCommentPj = function(){
        var text = $('.textarea-ct').val(),
            _id = $('.submit-btn-msg').data('id');
        var d = new Date,
        dformat =   [ d.getFullYear(),
                    ((d.getMonth()+1) < 10 ? '0'+(d.getMonth()+1) : (d.getMonth()+1)),
                    (d.getDate()< 10 ? '0'+d.getDate() : d.getDate())].join('-')+' '+
                    [(d.getHours() < 10 ? '0'+d.getHours() : d.getHours()),
                    (d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes()),
                    (d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds())].join(':');
        var link = listAvartar[employee_id];
        var content = text.replace(/\n/g, "<br>");
        if(text != ''){
            $.ajax({
                url: '/project_issues/saveComment',
                type: 'POST',
                data: {
                    id: _id,
                    model: 'project_issues',
                    content: text
                },
                success: function(success){
                    $('.modal-body').append('<div class="my-comment"><div class="my-date"><span>' + dformat + '</span></div><div class="my-content"><span>' + content + '</span></div></div><div class="right-avatar"><img class="avatar" src="'+link+'" alt="photo"></div>')
                    $('.textarea-ct').val('');
                }
            });
        }
    };
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
    function show_comment_popup( element){
        $(element).siblings('.template_logs').fadeToggle(300).toggleClass('active');
    }
    $('body').on('click', function(e){
        if(!( $(e.target).hasClass('flash-field') || $('.flash-field').find(e.target).length)){
            $('.template_logs').hide('300').removeClass('active');
        }
    });
    function sent_comment(element, model){
        if (!model) return;
        var _this = $(element);
        var _model = model;
        var _ele_append = _this.closest('.flash-field').find('.risk-log-content');
        var _comment_box = _this.closest('.template_logs').find('.add-comment-text');
        var loop = 1;
        var _comment = $.trim(_comment_box.val());
        if(_model && _comment){
            _comment_box.prop('disabled', true);
            _this.addClass('loading');
            _ele_append.addClass('loading');
            $.ajax({
                url: '<?php echo $html->url(array('controller' => 'project_risks_preview','action' => 'update_data_log')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    id: '',
                    model: _model,
                    description: _comment,
                    model_id: projectName['id'],
                    
                },
                success: function(response) {
                    var _html = '';
                    $.each(response, function(ind, data) {
                        var src = '',
                        employee_name = '';
                        console.log(data);
                        if(data){
                            if(is_avatar[data['employee_id']]){
                                var src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', data['employee_id']);
                                avatar = '<img title ="'+ data['update_by_employee'] +'" src ="'+ src + '" />';
                            }else{
                                employee_name = data['name'].split(" ");
                                first_name  =  employee_name[0].substr(0,1);
                                last_name  =  employee_name[1].substr(0,1);
                                avatar = first_name +''+ last_name;
                            }
                            _html +='<p class="circle-name">'+ avatar +'</p><p class="log-author" style="display: none">'+ data['name'] +'</p><span class="log-time">'+ data["created"] +'</span><a href="javascript:void(0)" class="log-field-edit"><img src="/img/new-icon/edit-task.png"></a><textarea data-log-id="'+ data['id'] +'" class="log-content not_save_history" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" onchange="updateLog.call(this, \'ProjectIssue\')">'+ data['description'] +'</textarea>';
                            if(_html) _ele_append.empty().append(_html);
                        }
                        _ele_append.removeClass('loading');
                       
                    });

                },
                complete: function(){
                    _comment_box.prop('disabled', false);
                }
            });
            
        }

    }
    $('.log-field-edit').click(function(){
        var text_area = $(this).closest('.risk-log-content').find('textarea');
        val = text_area.val();
        text_area.focus().val("").val(val);
    });
    function collapseScreen() {
        $('#table-collapse').hide();
        $('#expand').show();
        $('.wd-panel').removeClass('treeExpand');
        $(window).trigger('resize');
        // initresizable();
    }
    function expandScreen() {
        $('#table-collapse').show();
        $('#expand').hide();
        $('.wd-panel').addClass('treeExpand');
        $(window).trigger('resize');
        // destroyresizable();
    }
</script>
