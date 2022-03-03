<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'audit_missions', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- dialog_import -->
<div id="dialog_import_CSV" style="display:none;" title="Import CSV file" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'audit_missions', 'action' => 'import_csv', $company_id)));
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
                    <!--h2 class="wd-t1"><?php //echo __("Mission", true); ?></h2-->
                    <a href="<?php echo $this->Html->url(array('action' => 'update', $company_id));?>" id="add-activity" class="btn btn-plus"><span></span></a>
                    <!-- <a href="javascript:void(0);" id="export-submit" class="export-excel-icon-all" title="<?php __('Export Excel')?>"><span><?php __('Export') ?></span></a>  -->
                    <a href="<?php echo $this->Html->url(array('action' => 'export_excel'));?>" id="export-table" class="btn btn-excel"></a>
                    <?php if($displayImport === 'true'):?>
                    <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" title="<?php __('Import CSV')?>"><span><?php __('Import CSV') ?></span></a>
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
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

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
        'id' => 'mission_title',
        'field' => 'mission_title',
        'name' => __('Mission Title', true),
        'width' => 350,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.goToRecom'
    ),
    array(
        'id' => 'mission_number',
        'field' => 'mission_number',
        'name' => __('Mission Number', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'audit_setting_mission_status',
        'field' => 'audit_setting_mission_status',
        'name' => __('Mission Status', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'audit_setting_auditor_company',
        'field' => 'audit_setting_auditor_company',
        'name' => __('Auditor Company', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'audit_mission_manager',
        'field' => 'audit_mission_manager',
        'name' => __('Mission Manager', true),
        'width' => 320,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.GetManager'
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
    'audit_mission_manager' => !empty($employees) ? $employees : array()
);
App::import("vendor", "str_utility");
$str_utility = new str_utility();
if(!empty($auditMissions)){
    foreach($auditMissions as $auditMission){
        $data = array(
            'id' => $auditMission['id'],
            'no.' => $i++,
            'backupPM' => array(),
            'MetaData' => array()
        );
        $data['mission_title'] = (string) $auditMission['mission_title'];
        $data['mission_number'] = (string) $auditMission['mission_number'];
        $data['audit_setting_mission_status'] = (string) $auditMission['audit_setting_mission_status'];
        $data['audit_setting_auditor_company'] = (string) $auditMission['audit_setting_auditor_company'];
        if(!empty($missionManagers[$auditMission['id']])){
            foreach($missionManagers[$auditMission['id']] as $employId => $isBackup){
                $data['audit_mission_manager'][] = (string) $employId;
                $data['backupPM'][$employId] = !empty($isBackup) ? "1" : "0";
            }
        } else {
            $data['audit_mission_manager'] = array();
        }
        $data['action.'] = '';
        $dataView[] = $data;
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
    <a class="wd-edit" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'update', '%1$s', '%2$s')); ?>">Edit</a>
    <div class="wd-bt-big">
        <a onclick="return confirm('<?php echo h(sprintf(__('%s', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
    </div>
</div>
<script type="text/javascript">
    var DataValidator = {};
    (function($){
        $(function(){
            var $this = SlickGridCustom,
            company_id = <?php echo json_encode($company_id);?>,
            auditRecomOfMissions = <?php echo json_encode($auditRecomOfMissions);?>,
            urlRecom = <?php echo json_encode(urldecode($this->Html->link('%3$s', array('controller' => 'audit_recoms', 'action' => 'index', '%1$s', '%2$s')))); ?>;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var backupText = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    var msg = 'Are you sure you want to delete ' + dataContext.mission_title + '?';
                    if(auditRecomOfMissions && auditRecomOfMissions[dataContext.id] && auditRecomOfMissions[dataContext.id] != 0){
                        msg = <?php echo json_encode(__('Recommendations are associated with this mission, can you confirm the deletion of the mission and associated recommendations?', true));?>;
                    }
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate, company_id,
                    dataContext.id, msg), columnDef, dataContext);
                },
                goToRecom : function(row, cell, value, columnDef, dataContext) {
                    return Slick.Formatters.HTMLData(row, cell,$this.t(urlRecom, company_id, dataContext.id, value), columnDef, dataContext);
                },
                GetManager : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['audit_mission_manager'][val] + (dataContext.backupPM[val] == '1' ? backupText.text : ''));
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },
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
            var exporter = new Slick.DataExporter('/audit_missions/export_excel');
            ControlGrid.registerPlugin(exporter);

            $('#export-table').click(function(){
                exporter.submit();
                return false;
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
</style>
