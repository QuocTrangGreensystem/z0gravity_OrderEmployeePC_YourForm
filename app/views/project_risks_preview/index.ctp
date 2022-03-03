<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    'preview/project_risk.css?ver1.1', // Clean cache
    // 'preview/project_livrables'
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
$employeeAvatarLink = $this->Html->url('/img/avatar/%ID%.png');
if( empty( $synt_i18ns ) ) $synt_i18ns = array();
for ($m=1; $m<=12; $m++) {
	$month = date('M', mktime(0,0,0,$m, 1, 2000));
	$synt_i18ns[$m] = __($month,true);
	$synt_i18ns[$month] = __($month,true);
}
$canModified = (!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite);
?>
<style>
	.ui-datepicker .ui-datepicker-buttonpane {
		display: none;
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
                'url' => array('controller' => 'project_risks', 'action' => 'upload')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachment") ?></label>
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
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_risks', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<div id="template_logs" style="height: 440px; width: 320px;display: none;" class="wd-comment-dialog loading-mark wd-synthesis-comment-dialog">
    <div class="content-logs">
		<div class="comment">
			<?php if($canModified){?>
				<textarea data-log-id ="0" data-id="<?php echo $project_id;?>" cols="30" rows="6" class="synthesis-update-comment"></textarea>
			<?php } ?> 
		</div>
		<div class="content_comment"></div>
	</div>

</div>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="javascript:void(0);" class="btn btn-dashboard" title="<?php __('Dash Board') ?>"><span><?php __('Dash Board') ?></span>
                    </a>
                    <a href="javascript:void(0);" class="btn export-excel-icon-all" id="export-submit" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
               
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
                     <a href="javascript:void(0);" onclick="expandScreen();" class="btn btn-expand hide-on-mobile" id="expand"></a>
                     <a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapseScreen();" title="Collapse Tasks Screen" style="display: none;"></a>
                    <div class="risk-dashboard">
                        <div class="title-horizontal"><?php echo __('Severity', true) ?></div>
                        <div class="dashboard-content">
                            <ul class="row-dashboard">
                                <li class="item_2_0"><span class="text-horizontal"><?php echo __('FORTE', 'true');?></span>
									<div class="list-item"></div>
								</li>
                                <li class="item_2_1">
									<div class="list-item"></div>
								</li>
                                <li class="item_2_2">
									<div class="list-item"></div>
								</li>
                            </ul>
                            <ul class="row-dashboard">
                                <li class="item_1_0"><span class="text-horizontal"><?php echo __('MOYENNE', 'true');?></span>
									<div class="list-item"></div>
								</li>
                                <li class="item_1_1">
									<div class="list-item"></div>
								</li>
                                <li class="item_1_2">
									<div class="list-item"></div>
								</li>
                            </ul>
                            <ul class="row-dashboard">
                                <li class="item_0_0"><span class="text-horizontal"><?php echo __('FAIBLE', 'true');?></span>
                                <span class="text-vertical"><?php echo __('FAIBLE', 'true');?></span>
									<div class="list-item"></div>
								</li>
                                <li class="item_0_1"><span class="text-vertical"><?php echo __('MOYENNE', 'true');?></span>
									<div class="list-item"></div>
								</li>
                                <li class="item_0_2"><span class="text-vertical"><?php echo __('FORTE', 'true');?></span>
									<div class="list-item"></div>
								</li>
                            </ul>
                        </div>
                        <div class="title-vertical"><?php echo __('Occurrence', true) ?></div>
                        <ul class="list-dashboard"></ul>
                    </div>
                </div>
                <!-- div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div -->
                <div class="group-content">
					<?php  
					$normal = array("á", "à", "ả", "ã", "ạ", "ắ", "ằ", "ẳ", "ẵ", "ặ", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ố", "ồ", "ổ", "ỗ", "ộ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "Á", "À", "Ả", "Ã", "Ạ", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ỏ", "Õ", "Ọ", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "ă", "â", "ê", "ô", "ơ", "ư", "đ", "Ă", "Â", "Ê", "Ô", "Ò", "Ư", "Đ");
					$flat = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "a", "a", "e", "o", "o", "u", "d", "A", "A", "E", "O", "O", "U", "D");
					?>
                    <div id="risk-log" class="kpi-log">
                        <ul>
                            <li id="risk-log-last-item" class="flash-field">
                                <div class="risk-log-content">
									<?php if(!empty($logSystems)){
										$logSystem = $logSystems['LogSystem'];
										$linkAvatar = $this->UserFile->avatar($logSystem['employee_id']);
										$name = str_replace( $normal, $flat, $logSystem['name']);
										?>
										<p class="circle-name"><img title = "<?php echo $name ?>" src="<?php echo $linkAvatar ?>"></p>
										<p class="log-author" style="display: none"><?php echo $name ?></p>
										<span class="log-time"><?php echo date('d M Y', $logSystem['created']) ?></span>
										<?php if($canModified){ ?>
											<a href="javascript:void(0)" class="log-field-edit"><img src="/img/new-icon/edit-task.png"></a>
										<?php } ?> 
										<textarea data-log-id="<?php echo $logSystem['id'] ?>" class="log-content" id="risk-last-log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" <?php echo (!$canModified) ? 'disabled' : '' ?> onchange="updateLog.call(this, 'ProjectRisk')"><?php echo $logSystem['description'] ?></textarea>
									<?php } ?> 
                                </div>
								<?php if(!empty($logSystems) || $canModified){ ?>
									<a href="javascript:void(0);" onclick="show_comment_popup(this);" class="log-addnew"></a>
								<?php } ?> 
                            </li>
                            
                        </ul>
                    </div>
                </div>
                
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
				<?php if($canModified){ ?>
					<div class="popup-add">
						<a class="add-new-item" href="javascript:void(0);" onclick="addNewRisksButton();"><img title="<?php __('Add an item');?>" src="/img/new-icon/add.png"></a>
					</div>
				<?php } ?> 
                <div class="wd-table" id="project_container" style="width:100%; height: 400px">

                </div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php //echo $this->element('grid_status'); ?>
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
{

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
    array(
        'id' => 'project_risk',
        'field' => 'project_risk',
        'name' => __('Risk/Opportunity', true),
        'width' => 250,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    // array(
    //     'id' => 'file_attachement',
    //     'field' => 'file_attachement',
    //     'name' => __('Attachement or URL', true),
    //     'width' => 150,
    //     'noFilter' => 1,
    //     'sortable' => false,
    //     'resizable' => false,
    //     'editor' => 'Slick.Editors.Attachement',
    //     'formatter' => 'Slick.Formatters.Attachement'
    // ),
    array(
        'id' => 'project_risk_severity_id',
        'field' => 'project_risk_severity_id',
        'name' => __('Severity', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'formatter' => 'Slick.Formatters.contextSeverities'
    ),
    array(
        'id' => 'project_risk_occurrence_id',
        'field' => 'project_risk_occurrence_id',
        'name' => __('Occurrence', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'formatter' => 'Slick.Formatters.contextOccurrences'
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
    array(
        'id' => 'risk_assign_to',
        'field' => 'risk_assign_to',
        'name' => __('Assign to', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
    ),
    array(
        'id' => 'actions_manage_risk',
        'field' => 'actions_manage_risk',
        'name' => __('Action Related', true),
        'width' => 250,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textArea',
        'formatter' => 'Slick.Formatters.actionManageRisk'
    ),
    array(
        'id' => 'risk_close_date',
        'field' => 'risk_close_date',
        'name' => __('Date closing', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __(' ', true),
        'width' => 40,
        'maxWidth' => 40,
        'sortable' => false,
        'resizable' => false,
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
    'project_risk_severity_id' => $riskSeverities,
    'project_risk_occurrence_id' => $riskOccurrences,
    'project_issue_status_id' => $issueStatus,
    'risk_assign_to' => $employees,
);

foreach ($projectRisks as $projectRisk) {
    $data = array(
        'id' => $projectRisk['ProjectRisk']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );

    $data['project_risk_severity_id'] = $projectRisk['ProjectRisk']['project_risk_severity_id'];
    $data['project_risk_occurrence_id'] = $projectRisk['ProjectRisk']['project_risk_occurrence_id'];
    $data['project_issue_status_id'] = $projectRisk['ProjectRisk']['project_issue_status_id'];
    $data['risk_assign_to'] = $projectRisk['ProjectRisk']['risk_assign_to'];

    $data['project_risk'] = $projectRisk['ProjectRisk']['project_risk'];
    $data['actions_manage_risk'] = (string)$projectRisk['ProjectRisk']['actions_manage_risk'];

    $data['risk_close_date'] = $str_utility->convertToVNDate($projectRisk['ProjectRisk']['risk_close_date']);

    $data['file_attachement'] = (string) $projectRisk['ProjectRisk']['file_attachement'];
    $data['format'] = (string) $projectRisk['ProjectRisk']['format'];
    $data['weight'] = $projectRisk['ProjectRisk']['weight'];
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
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>

<div id="collapse" style="padding:4px; cursor:pointer; background-color:#FFF; display:none; position: fixed; top:0; right:0; z-index:9999999999" onclick="collapseScreen();" >
    <button class="btn btn-esc"></button>
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
    var DateValidate = {},dataGrid,IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);
    };
    var projectName = <?php echo json_encode($projectName['Project']); ?>;
    var riskOccurrences_color = <?php echo json_encode($riskOccurrences_color); ?>;
    var riskSeverities_color = <?php echo json_encode($riskSeverities_color); ?>;
    var risk_action_update = <?php echo json_encode($risk_action_update) ?>;
    var employee_update = <?php echo json_encode($employee_update) ?>;
	var link_avatar = <?php echo json_encode($employeeAvatarLink); ?>;
	var synt_i18ns = <?php echo json_encode($synt_i18ns); ?>;
	var canModified = <?php echo json_encode($canModified); ?>;
    $('.btn-dashboard').on('click', function(){
        var ele_content = $('.risk-dashboard');
        var list_dashboard = $('.list-dashboard');
        $('.risk-dashboard').toggleClass('active');
        if(! ele_content.hasClass('active')) return;
        $.ajax({
            url : '<?php echo $html->url('/project_risks_preview/dashboard/' . $projectName['Project']['id']) ?>',
            type : 'POST',
            dataType : 'json',
            success : function(data){
                _list = '';
                if(data){
                    $('.risk-dashboard').removeClass('loading');
                    $('.item-dash').remove();
                    $.each(data, function(ind, _data) {
                        ele_content.find(".item_" +_data['dashboard_type']).find('.list-item').append('<p class="item-dash" title="'+ _data['project_risk']+'">' +  _data.weight + '</p>');
                        _list += '<li class="list_'+_data['dashboard_type']+'"><span class="circle">' +  _data.weight + '</span> '+ _data['project_risk']+' </li>';

                    });
                }
                list_dashboard.empty().append(_list);
                // var dashboard_cell = $('.dashboard-content ul li');
                // dashboard_cell.each(function(){
                    // var _item = $(this).find('.item-dash');
                    // var i = 0;
                    // if(_item.length>1){
                        // _item.each(function(){
                            // $(this).css('margin-top', 10*i++);
                        // });
                    // }
                // });

            },
            beforeSend : function(){
                $('.risk-dashboard').addClass('loading');
            },
            error: function(){
                $('.risk-dashboard').removeClass('loading');
            }
        });
        
    
    });
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
            $this.canModified =  <?php echo json_encode($canModified); ?>;
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
                }else {
                    //_valid = value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']);
                    //_message = $this.t('Date closing must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date']);
                    // _valid = value >= getTime(projectName['start_date']);
                    // _message = $this.t('Date closing must larger %1$s' ,projectName['start_date']);
                    _valid = true;
                    _message = '';
                }
                return {
                    valid : _valid,
                    message : _message
                };
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
                    dataContext.project_id,dataContext.project_risk), columnDef, dataContext);
                },
                contextOccurrences: function(row, cell, value, columnDef, dataContext){
                   
                    if(riskOccurrences_color[value]){
                        color = ['blue','yellow','red'];
                        return '<span class="risk-color risk-'+ color[riskOccurrences_color[value]['value_risk_occurrence']]+'"></span><span>' + riskOccurrences_color[value]['risk_occurrence'] + '</span>';
                    }
                    return '';
                },
                contextSeverities: function(row, cell, value, columnDef, dataContext){
                    if(riskSeverities_color[value]){
                        color = ['blue','yellow','red'];
                        _content = riskSeverities_color[value]['risk_severity'] ? riskSeverities_color[value]['risk_severity'] : '';
                        return '<span class="risk-color risk-'+ color[riskSeverities_color[value]['value_risk_severitie']]+'"></span><span>' + _content + '</span>';
                    }
                    return '';
                },
                actionManageRisk: function(row, cell, value, columnDef, dataContext){
                    var _html = '';
                    var avatar = src = '',
                        employee_name = '';
                    if(risk_action_update && risk_action_update[dataContext['id']]){
                        employee_id = risk_action_update[dataContext['id']]['employee_id'];
						if( employee_id in employee_update){
							employee_name = employee_update[employee_id]['fullname'];
							var src = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', employee_id);
							avatar = '<span class="circle-name" title="' + employee_name + '"><img src ="'+ src + '" /></span>';
						}
                    }
                    return avatar + value;
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
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_risk_severity_id : {defaulValue : ''},
                project_issue_status_id : {defaulValue : ''},
                project_risk_occurrence_id : {defaulValue : ''},
                risk_assign_to : {defaulValue : ''},
                project_risk : {defaulValue : ''},
                actions_manage_risk : {defaulValue : ''},
                risk_close_date : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                weight : {defaulValue : 0 }
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            // dataGrid = $this.init($('#project_container'),data,columns, {
            //     frozenColumn: 2
            // });
            dataGrid = $this.init($('#project_container'),data,columns, get_grid_option());
             /*
            add colums grid new
             */
            ///ControlGrid = $this.init($('#project_container'),data,columns);
            addNewRisksButton = function(){
                dataGrid.gotoCell(data.length, 1, true);
            }
            $this.onBeforeEdit = function(args){
                if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
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
                console.log(text);
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
                    url : '<?php echo $html->url('/project_risks/order/' . $projectName['Project']['id']) ?>',
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
                //     $(ind).css('border', 'solid 2px orange');
                    check = true;
                // } else {
                //     $(ind).css('border', 'none');
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
        employeeLoginId = <?php echo json_encode($employeeLoginId);?>;
    function addLog(id){
        var nl = $(id).find('.new-log').toggle();
        if( nl.is(':visible') ){
            nl.find('.log-content').focus();
        }
    }
    function updateLog(model){
        var inp = $(this);
        var value = $.trim(inp.val()),
            log_id = inp.data('log-id');
            li = inp.closest('.risk-log-content');
        if( value ){
            // loading
            inp.prop('disabled', true);
            // save
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'update_data_log')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    id: log_id,
                    company_id: company_id,
                    model: model,
                    model_id: projectName['id'],
                    name: li.find('.log-author').text(),
                    description: value,
                    employee_id: employeeLoginId,
                    update_by_employee: employeeLoginName
                    
                },
                success: function(response) {
                    console.log(response);
                    var data = response.LogSystem;
                    if( !log_id ){
                        var newLi = li.clone();
                        newLi.removeClass('new-log');
                        newLi.find('.log-content').prop('rowspan', 1);
                        newLi.find('.log-time').text(data.date_updated);
                        newLi.data('log-id', data.id);
                        newLi.insertAfter(li);
                        // reset li
                        inp.val('').prop('disabled', false);
                        // reset new Li
                        inp = newLi.find('.log-content');
                        // hide
                        li.hide();
                    }else{
						li.find('.log-time').text(data.date_updated);
						li.find('.log-author').text(data.name);
						var avatar = link_avatar.replace('%ID%', data.employee_id);
						avatar = '<img width="30" height="35" src="' + avatar + '" title="' + data.name + '"/>';
						li.find('.circle-name:first').html(avatar);
					}
                },
                complete: function(){
                    // hide loading
                    // can change
                    inp.prop('disabled', false).css('color', '#3BBD43');
					setTimeout( function(){
						inp.css('color', '');
					}, 3000);
                }
            });
        }
    }
    function show_comment_popup( element){
        var _html = '';
        var latest_update = '';
        // var log_id = $(element).data('log-id');
        var popup = $('#template_logs');
		$.ajax({
            url: '/projects_preview/getComment',
            type: 'POST',
            data: {
                id: projectName['id'],
                model: 'ProjectRisk',
            },
            dataType: 'json',
			beforeSend: function(){
				popup.dialog({
					position: 'center',
					autoOpen: false,
					modal: true,
					width: 520,
					minHeight: 50,
				});
				popup.dialog('option', {title: projectName['project_name']}).dialog('open');
				popup.addClass('loading');
			},
            success: function (data) {
				if( data.comment)
					draw_risk_comment(data.comment, 0);
			},
			// error: function (data) {
                // location.reload();
			// },
			complete: function(){
				popup.removeClass('loading');
			}
        });
    }
	function draw_risk_comment(comments, log_id){
		var popup = $('#template_logs');
		var comment_list = '';
		var edit_comment = '';
		$.each(comments, function (ind, _data) {
			var comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
			date = _data['updated'];
			var avatar = link_avatar.replace('%ID%', _data['employee_id']);
			var ava_src = '<img width = 35 height = 35 src="' + avatar + '" title = "' + _data['name'] + '" />';
			comment_list += '<div class="content comment-content-' + _data['id'] + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + _data['name'] + '</p><div class="comment">' + comment + '</div></div></div>';
			if( _data['id'] == log_id){
				edit_comment = _data['description'];
				popup.find('.synthesis-update-comment').data('log-id', log_id).val(edit_comment);
			}
		});
		popup.find('.content_comment').html(comment_list);
	}
	$('#template_logs').on("change", ".synthesis-update-comment", function () {
		var _this = $(this);
        var text = _this.val(),
			field = 'ProjectRisk',
			id = _this.data("id"),
			logid = _this.data("log-id"),
			html = _layout_html = '';
		var popup = $('#template_logs');

        if (text != '') {
            _this.closest('.loading-mark').addClass('loading');
            $.ajax({
                url: '/projects_preview/update',
                type: 'POST',
                dataType: 'json',
                data: {
                    data: {
                        id: id,
                        text: text,
                        field: field,
                        logid: logid,
                    }
                },
                success: function (result) {
					var _data = '';
					if( logid){
						$.each(result, function(ind, comm){
							if( comm['id'] == logid){
								_data = comm;
							}
						});
					}else{
						_data = result[0];
					}
                    if( _data){
						popup.find('.comment-content-' + _data['id']).remove();
						
						name = ava_src = '';
						comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
						date = _data['updated'];
						var avatar = link_avatar.replace('%ID%', _data['employee_id']);
						ava_src += '<img width = 35 height = 35 src="' + avatar + '" title = "' + _data['name'] + '" />';
						// update popup
						html += '<div class="content comment-content-' + _data['id'] + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + _data['name'] + '</p><div class="comment">' + comment + '</div></div></div>';
						popup.find('.content_comment').prepend(html);
						
						// update last comment
						var updated = _data['updated'];
						updated = updated.split('/');
						updated = updated[0] + ' ' + synt_i18ns[ parseInt(updated[1]) ] + ' ' + updated[2];
						_description = _data['description'];
						_layout_html += '<p class="circle-name" title = "' + _data['name'] + '">' + ava_src + '"></p><span class="log-time">' + updated + '</span>';
						if(canModified){
							_layout_html += '<a href="javascript:void(0)" class="log-field-edit"><img src="/img/new-icon/edit-task.png"></a>';
						}
						_layout_html += '<textarea data-log-id="' + _data['id'] + '" class="log-content" id="risk-last-log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" <?php echo (!$canModified) ? 'disabled' : '' ?> onchange="updateLog.call(this, "ProjectRisk")">' + _description + '</textarea>';
						$('#risk-log').find('.risk-log-content').html(_layout_html);
						_this.val('');
					}
                    
                },
				error: function (data) {
					location.reload();
				},
				complete: function(){
					_this.closest('.loading-mark').removeClass('loading');
				}
            });
        }
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
    
    $('#risk-log-last-item').on('click', '.log-field-edit', function(){
        var text_area = $(this).closest('.risk-log-content').find('textarea');
        val = text_area.val();
        text_area.focus().val("").val(val);
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
</script>
