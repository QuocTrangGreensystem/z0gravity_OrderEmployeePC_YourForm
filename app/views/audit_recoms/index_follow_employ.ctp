<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'audit_recoms', 'action' => 'export_follow_employ')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- dialog_import -->
<div id="dialog_import_CSV" style="display:none;" title="Import CSV file" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'audit_recoms', 'action' => 'import_csv', $company_id)));
    ?>
    <div class="wd-input" style="margin-left: -33px;">
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
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="<?php echo $this->Html->url(array('action' => 'export_excel_follow_employ'));?>" id="export-table" class="btn btn-excel"></a>
                    <!-- <a href="javascript:void(0);" id="export-submit" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export') ?></span></a>  -->
                    <?php if($displayImport === 'true'):?>
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" style="margin-right:5px; " title="<?php __('Import CSV')?>"><span><?php __('Import CSV') ?></span></a>
                    <?php endif;?>
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
                <div id="pager" style="width:100%;height:36px; overflow: hidden; margin-top: 30px;">

                </div>
                <?php //echo $this->element('grid_status'); ?>
            </div>
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
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'new_recom',
        'field' => 'new_recom',
        'name' => __(' ', true),
        'width' => 45,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.ActionNewRecom'
    ),
    array(
        'id' => 'mission_title',
        'field' => 'mission_title',
        'name' => __('Title', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionFormat'
    ),
    array(
        'id' => 'mission_number',
        'field' => 'mission_number',
        'name' => __('Number', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionFormat'
    ),
    array(
        'id' => 'audit_setting_mission_status',
        'field' => 'audit_setting_mission_status',
        'name' => __('Status', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionFormat'
    ),
    array(
        'id' => 'audit_setting_auditor_company',
        'field' => 'audit_setting_auditor_company',
        'name' => __('Auditor Company', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionFormat'
    ),
    array(
        'id' => 'audit_mission_manager',
        'field' => 'audit_mission_manager',
        'name' => __('Manager', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.GetManager'
    ),
    array(
        'id' => 'id_recommendation',
        'field' => 'id_recommendation',
        'name' => __('ID Recommendation', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionRecom'
    ),
    array(
        'id' => 'contact',
        'field' => 'contact',
        'name' => __('Statement', true), // Statement = Contact
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionRecom'
    ),
    array(
        'id' => 'recommendation',
        'field' => 'recommendation',
        'name' => __('Recommendation', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.GoToReComDetail'
    ),
    array(
        'id' => 'audit_setting_recom_priority',
        'field' => 'audit_setting_recom_priority',
        'name' => __('Priority', true),
        'width' => 80,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionRecom'
    ),
    array(
        'id' => 'audit_setting_recom_status_mission',
        'field' => 'audit_setting_recom_status_mission',
        'name' => __('Status (Mission Manager)', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionRecom'
    ),
    array(
        'id' => 'audit_recom_manager',
        'field' => 'audit_recom_manager',
        'name' => __('Manager', true),
        'width' => 220,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.GetManagerRecom'
    ),
    array(
        'id' => 'implement_date',
        'field' => 'implement_date',
        'name' => __('Initial Implementation Date', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.MisionRecom'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'ignoreExport' => true,
        'formatter' => 'Slick.Formatters.Action'
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array(
    'audit_setting_mission_status' => !empty($auditSettings[1]) ? $auditSettings[1] : array(),
    'audit_setting_auditor_company' => !empty($auditSettings[0]) ? $auditSettings[0] : array(),
    'audit_mission_manager' => !empty($employees) ? $employees : array(),
    'audit_setting_recom_priority' => !empty($auditSettings[4]) ? $auditSettings[4] : array(),
    'audit_setting_recom_status_mission' => !empty($auditSettings[3]) ? $auditSettings[3] : array(),
    'audit_recom_manager' => !empty($employees) ? $employees : array()
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($auditRecoms)){
    foreach($auditRecoms as $missionId => $auditRecom){
        foreach($auditRecom as $recomId => $value){
            $data = array(
                'id' => $value['id'],
                'no.' => $i++,
                'backupPM' => array(),
                'backupPMRecom' => array(),
                'MetaData' => array()
            );
            // Mission
            $data['audit_mission_id'] = $missionId;
            $data['mission_title'] = (string) $auditMissions[$missionId]['mission_title'];
            $data['mission_number'] = (string) $auditMissions[$missionId]['mission_number'];
            $data['audit_setting_mission_status'] = (string) $auditMissions[$missionId]['audit_setting_mission_status'];
            $data['audit_setting_auditor_company'] = (string) $auditMissions[$missionId]['audit_setting_auditor_company'];
            if(!empty($auditMissionEmployees[$missionId])){
                foreach($auditMissionEmployees[$missionId] as $employId => $isBackup){
                    $data['audit_mission_manager'][] = (string) $employId;
                    $data['backupPM'][$employId] = !empty($isBackup) ? "1" : "0";
                }
            } else {
                $data['audit_mission_manager'] = array();
            }
            // Recommendation
            $data['id_recommendation'] = (string) $value['id_recommendation'];
            $data['contact'] = (string) $value['contact'];
            $data['recommendation'] = (string) $value['recommendation'];
            $data['audit_setting_recom_priority'] = (string) $value['audit_setting_recom_priority'];
            $data['audit_setting_recom_status_mission'] = (string) $value['audit_setting_recom_status_mission'];
            if(!empty($missionManagerRecoms[$value['id']])){
                foreach($missionManagerRecoms[$value['id']] as $employId => $isBackup){
                    $data['audit_recom_manager'][] = (string) $employId;
                    $data['backupPMRecom'][$employId] = !empty($isBackup) ? "1" : "0";
                }
            } else {
                $data['audit_recom_manager'] = array();
            }
            $data['implement_date'] = !empty($value['implement_date']) ? date('d/m/Y', $value['implement_date']) : '';
            $data['action.'] = '';
            $dataView[] = $data;
        }
    }
}
$i18n = array(
    'The Activity has already been exist.' => __('The Activity has already been exist.', true),
    'The date must be smaller than or equal to %s' => __('The date must be smaller than or equal to %s', true),
    'The date must be greater than or equal to %s' => __('The date must be greater than or equal to %s', true),
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'update', '%1$s', '%2$s', '%4$s', '%5$s')); ?>">Edit</a>
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s', '%4$s', '%5$s')); ?>">Delete</a>
    </div>
</div>
<div id="action-template-new-recom" style="display: none;">
    <div>
        <a href="<?php echo $this->Html->url(array('action' => 'update', '%1$s', '%2$s', '-1', 'follow_employ')); ?>" class="gs-add-invoi">
            <span></span>
        </a>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            nameMission = <?php echo json_encode(__('Mission', true));?>,
            nameRecom = <?php echo json_encode(__('Recommendation', true));?>,
            //editMission = <?php //echo json_encode($editMission); ?>,
            urlRecomDetails = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('action' => 'update', '%1$s', '%2$s', '%4$s', 'follow_employ')))); ?>;
            company_id = <?php echo json_encode($company_id);?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html(),
            actionTemplateNewRecom = $('#action-template-new-recom').html();
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, company_id,
                        dataContext.audit_mission_id, dataContext.recommendation, dataContext.id, 'follow_employ'), columnDef, dataContext);
                },
                GetManager : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['audit_recom_manager'][val] + (dataContext.backupPM[val] == '1' ? backupText.text : ''));
                    });
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent">' + _value.join(', ') + '</span>', columnDef, dataContext);
                },
                GetManagerRecom : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['audit_recom_manager'][val] + (dataContext.backupPMRecom[val] == '1' ? backupText.text : ''));
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },
                MisionFormat : function(row, cell, value, columnDef, dataContext){
                    var _value = '';
                    if(columnDef.id == 'audit_setting_mission_status'){
                        _value = $this.selectMaps['audit_setting_mission_status'][value] ? $this.selectMaps['audit_setting_mission_status'][value] : '';
                    } else if(columnDef.id == 'audit_setting_auditor_company'){
                        _value = $this.selectMaps['audit_setting_auditor_company'][value] ? $this.selectMaps['audit_setting_auditor_company'][value] : '';
                    } else {
                        _value = value ? value : '';
                    }
                    return '<span class="row-parent">' + _value + '</span>';
                },
                MisionRecom : function(row, cell, value, columnDef, dataContext){
                    var _value = '';
                    if(columnDef.id == 'audit_setting_recom_priority'){
                        _value = $this.selectMaps['audit_setting_recom_priority'][value] ? $this.selectMaps['audit_setting_recom_priority'][value] : '';
                    } else if(columnDef.id == 'audit_setting_recom_status_mission'){
                        _value = $this.selectMaps['audit_setting_recom_status_mission'][value] ? $this.selectMaps['audit_setting_recom_status_mission'][value] : '';
                    } else {
                        _value = value ? value : '';
                    }
                    return _value;
                },
                ActionNewRecom : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplateNewRecom, company_id,
                            dataContext.audit_mission_id), columnDef, dataContext);
                },
                GoToReComDetail : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlRecomDetails, company_id, dataContext.audit_mission_id, dataContext.recommendation, dataContext.id), columnDef, dataContext);
                }
            });;
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns, {
                enableAddRow : false,
                editable: false
            });
            var exporter = new Slick.DataExporter('/audit_recoms/export_excel_follow_employ');
            ControlGrid.registerPlugin(exporter);

            $('#export-table').click(function(){
                exporter.submit();
                return false;
            });
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-parent').parent().addClass('row-parent-custom');
            var headers = $('.slick-header-columns').get(0).children;
            $.each(headers, function(index, val){
                if(index > 6 && index < 14){
                    $('#'+headers[index].id).addClass('gs-custom-cell-md-header');
                    $('#'+headers[index].id).addClass('border-md-custom');
                }
            });
            header =
                '<div id="wd-header-custom" class="slick-headerrow-columns" style="margin-left: -1px;">'
                    + '<div class="slick-headerrow-column l0 r0 gs-custom-cell-euro-header fist-element border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l1 r1 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l2 r2 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l3 r3 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l4 r4 gs-custom-cell-euro-header border-euro-custom"><span>' +nameMission+ '</span></div>'
                    + '<div class="slick-headerrow-column l5 r5 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l6 r6 gs-custom-cell-euro-header border-euro-custom"></div>'
                    + '<div class="slick-headerrow-column l7 r7 gs-custom-cell-md-header"></div>'
                    + '<div class="slick-headerrow-column l8 r8 gs-custom-cell-md-header"></div>'
                    + '<div class="slick-headerrow-column l9 r9 gs-custom-cell-md-header"></div>'
                    + '<div class="slick-headerrow-column l10 r10 gs-custom-cell-md-header"></div>'
                    + '<div class="slick-headerrow-column l11 r11 gs-custom-cell-md-header"><span class="custom-title">' +nameRecom+ '</span></div>'
                    + '<div class="slick-headerrow-column l12 r12 gs-custom-cell-md-header"></div>'
                    + '<div class="slick-headerrow-column l13 r13 gs-custom-cell-md-header"></div>'
                    + '<div class="slick-headerrow-column l14 r14 gs-custom-cell-euro-header"></div>'
              + '</div>';
            $('.slick-header-columns').before(header);
            // khi keo scroll thi to mau cac cell
            ControlGrid.onScroll.subscribe(function(args, e, scope){
                $('.row-parent').parent().addClass('row-parent-custom');
                $('.row-disabled').parent().addClass('row-disabled-custom');
                $('.row-number').parent().addClass('row-number-custom');
            });
            $('#dialog_import_CSV').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 360,
                height      : 150
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
            $(".cancel").live('click',function(){
                $("#dialog_import_CSV").dialog("close");
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
        });

    })(jQuery);
</script>
<?php
    echo $html->css(array(
        'jquery.multiSelect',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
        'audit'
    ));
    echo $html->script(array(
        'history_filter',
        'jquery.multiSelect',
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom',
        'responsive_table.js',
        'slick_grid/slick.grid.activity',
        'slick_grid/plugins/slick.dataexporter',
    ));
    echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
.slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
.gs-add-invoi span{padding-right: 8px !important;}
</style>
