<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
	'preview/project_decisions'
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
    'jquery.ui.touch-punch.min'
));
echo $this->element('dialog_projects');
?>
<style>
    .row-number{
        float: right;
    }
    .row-date{
        text-align: center;
    }
    .transfer {
        cursor: pointer;
        float: left;
        display: inline-block;
		margin-top: 8px;
    }
    .wd-bt-big {
        margin-left: 0;
		display: inline-block;
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
        padding: 5px 15px;
        border: 1px solid #d0d0d0;
        cursor: pointer;
        border-radius: 4px;
    }
    .gradient:hover,
    .gradient:focus {
        border-color: #bbb;
    }
    .gradient.g {
        background: rgb(0,110,46);
        background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzAwNmUyZSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMwMDZlMmUiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
        background: -moz-linear-gradient(top, rgba(0,110,46,1) 0%, rgba(0,110,46,1) 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,110,46,1)), color-stop(100%,rgba(0,110,46,1)));
        background: -webkit-linear-gradient(top, rgba(0,110,46,1) 0%,rgba(0,110,46,1) 100%);
        background: -o-linear-gradient(top, rgba(0,110,46,1) 0%,rgba(0,110,46,1) 100%);
        background: -ms-linear-gradient(top, rgba(0,110,46,1) 0%,rgba(0,110,46,1) 100%);
        background: linear-gradient(to bottom, rgba(0,110,46,1) 0%,rgba(0,110,46,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#006e2e', endColorstr='#006e2e',GradientType=0 );
        color: #fff;
    }
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
                'url' => array('controller' => 'project_evolutions', 'action' => 'upload')
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
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_evolutions', 'action' => 'export', $projectName['Project']['id'])));
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
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="btn btn-gantt" title="<?php __('Gantt+') ?>"><span><?php __('Gantt+') ?></span></a>
                    <a href="javascript:void(0);" class="btn export-excel-icon-all" id="export-submit" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Reset filter') ?>"></a>
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
                <div id="pager" style="width:100%;height:0px; overflow: hidden; margin-top: 30px;">

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
        'id' => 'project_evolution',
        'field' => 'project_evolution',
        'name' => __('Evolution', true),
        'width' => 200,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'file_attachement',
        'field' => 'file_attachement',
        'name' => __('Attachement or URL', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        'editor' => 'Slick.Editors.Attachement',
        'formatter' => 'Slick.Formatters.Attachement'
    ),
    array(
        'id' => 'project_evolution_type_id',
        'field' => 'project_evolution_type_id',
        'name' => __('Type Evolution', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'evolution_applicant',
        'field' => 'evolution_applicant',
        'name' => __('Required By', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'evolution_date_validated',
        'field' => 'evolution_date_validated',
        'name' => __('Date Validated', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
        'formatter'=>'numberValDate'

    ),
    array(
        'id' => 'evolution_validator',
        'field' => 'evolution_validator',
        'name' => __('Validated By', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'evolution_impact',
        'field' => 'evolution_impact',
        'name' => __('Impact', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'phase_id',
        'field' => 'phase_id',
        'name' => __('Phase', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        //'validator' => 'DateValidate.budgetBox'
    ),
    array(
        'id' => 'progress',
        'field' => 'progress',
        'name' => __('Progress %', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.forecastValue',
        //'validator' => 'DateValidate.budgetBox'
        'formatter'=>'floatNumber'
    ),
    array(
        'id' => 'due_date',
        'field' => 'due_date',
        'name' => __('Due date', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.budgetBox'
        'formatter'=>'numberValDate'
    ),
    array(
        'id' => 'supplementary_budget',
        'field' => 'supplementary_budget',
        'name' => __('Budget', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.forecastValue',
        'formatter'=>'currency'
    ),
    array(
        'id' => 'man_day',
        'field' => 'man_day',
        'name' => __('M.D', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.forecastValue',
        'formatter'=>'numberValManDay'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __(' ', true),
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
    'project_evolution_type_id' => $evolutionTypes,
    'evolution_impact' => $projectEvolutionImpacts,
    'evolution_applicant' => $employees,
    'evolution_validator' => $employees,
    'phase_id' => $phaseLoads
);
foreach ($projectEvolutions as $projectEvolution) {
    $data = array(
        'id' => $projectEvolution['ProjectEvolution']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++
    );
    $data['project_evolution'] = $projectEvolution['ProjectEvolution']['project_evolution'];
    $data['project_evolution_type_id'] = $projectEvolution['ProjectEvolution']['project_evolution_type_id'];
    $data['evolution_applicant'] = $projectEvolution['ProjectEvolution']['evolution_applicant'];
    $data['phase_id'] = $projectEvolution['ProjectEvolution']['phase_id'];


    $data['evolution_validator'] = $projectEvolution['ProjectEvolution']['evolution_validator'];
    $data['supplementary_budget'] = $projectEvolution['ProjectEvolution']['supplementary_budget'];
    $data['man_day'] = $projectEvolution['ProjectEvolution']['man_day'];
    $data['weight'] = $projectEvolution['ProjectEvolution']['weight'];

    $data['evolution_impact'] = array();
    if (!empty($projectEvolution['ProjectEvolutionImpactRefer'])) {
        $data['evolution_impact'] = Set::classicExtract($projectEvolution['ProjectEvolutionImpactRefer'], '{n}.project_evolution_impact_id');
    }

    $data['evolution_date_validated'] = $str_utility->convertToVNDate(
            $projectEvolution['ProjectEvolution']['evolution_date_validated']);
    $data['due_date'] = $str_utility->convertToVNDate(
            $projectEvolution['ProjectEvolution']['due_date']);
    $data['progress'] = $projectEvolution['ProjectEvolution']['progress'];
    $data['file_attachement'] = (string) $projectEvolution['ProjectEvolution']['file_attachement'];
    $data['format'] = (string) $projectEvolution['ProjectEvolution']['format'];

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
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 65px;">
        <a onclick="openDialog('%1$s', '%2$s')" title="<?php echo h(__('Transfer', true)) ?>" class="transfer"><img src="<?php echo $this->Html->url('/') ?>img/transfer-icon.png" alt="" /></a>
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="select-project-dialog" style="display: none;" title="">
    <form method="post" action="<?php echo $this->Html->url('/') ?>project_evolutions/transfer">
        <select name="data[newId]" style="font-size: 12px; margin: 2px; padding: 3px">
        <?php foreach( $projectList as $id => $name ): ?>
            <option value="<?php echo $id ?>"><?php echo $name ?></option>
        <?php endforeach; ?>
        </select><br/>
        <div style="text-align: right; border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px">
            <button type="submit" id="select-ok" class="gradient g"><?php __('Transfer') ?></button>
            <button type="button" id="select-cancel" onclick="$('#select-project-dialog').dialog('close')" class="gradient"><?php __('Cancel') ?></button>
        </div>
        <input type="hidden" name="data[eid]" id="eid">
        <input type="hidden" name="data[pid]" id="pid">
    </form>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
// heightTable = (heightTable < 550) ? 550 : heightTable;
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
$('#select-project-dialog').dialog({
    position    :'center',
    autoOpen    : false,
    autoHeight  : true,
    modal       : true,
    width       : 600,
    height      : 120
});
function openDialog(eid, pid){
    $('#eid').val(eid);
    $('#pid').val(pid);
    $('#select-project-dialog').dialog('open');
}
    var DateValidate = {},dataGrid,IuploadComplete = function(json){
        var data = dataGrid.eval('currentEditor');
        data.onComplete(json);
    };
    function number_format (number, decimals, dec_point, thousands_sep) {

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
    };
    var numberValManDay = function(row, cell, value, columnDef, dataContext){
        value = value ? value : 0;
        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' <?php __('M.D') ?></span> ', columnDef, dataContext);
    };
    var currency = function(row, cell, value, columnDef, dataContext){
        value = value ? value : 0;
        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' &euro;</span> ', columnDef, dataContext);
    };
    var floatNumber = function(row, cell, value, columnDef, dataContext){
        value = value ? value : 0;
        return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + '</span> ', columnDef, dataContext);
    };
    var numberValDate = function(row, cell, value, columnDef, dataContext){
        return Slick.Formatters.HTMLData(row, cell, '<span class="row-date">' + value + '</span> ', columnDef, dataContext);
    };
    (function($){

        $(function(){
            var $this = SlickGridCustom, gridControl;

            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = dataGrid.getDataItem(row);
                if(data && confirm($this.t('Delete attachment %s?'
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
            $this.canModified =  <?php echo json_encode((!empty($canModified)  && !$_isProfile )|| ($_isProfile && $_canWrite)); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

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
                forecastValue : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 11).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(!/^([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });


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
            DateValidate.budgetBox = function(value){
                return {
                    valid : /^([1-9]|[1-9][0-9]*)$/.test(value) && parseInt(value ,10) > 0,
                    message : $this.t('The Budget must be a number and greater than 0')
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
                    dataContext.project_id,dataContext.project_evolution), columnDef, dataContext);
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : true},
                project_evolution : {defaulValue : '' , allowEmpty : true},
                project_evolution_type_id : {defaulValue : ''},
                supplementary_budget : {defaulValue : ''},
                evolution_applicant : {defaulValue : ''},
                phase_id : {defaulValue : ''},
                evolution_impact : {defaulValue : []},
                evolution_validator : {defaulValue : ''},
                evolution_date_validated : {defaulValue : ''},
                due_date : {defaulValue : ''},
                man_day : {defaulValue : ''},
                progress : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                weight : {defaulValue : 0 }
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            dataGrid = gridControl = $this.init($('#project_container'),data,columns, {
                frozenColumn: 2,
				rowHeight: 40,
				headerRowHeight: 40, 
            });
            $this.onBeforeEdit = function(args){
                if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
                    return false;
                }
                return true;
            }

            var _sumBudget = <?php echo json_encode($sumBudget); ?>;
            var _sumManDay = <?php echo json_encode($sumManDay); ?>;

            $this.onCellChange = function(args){
                if(args.item){
                    var _sumBudget = 0;
                    var _sumManDay = 0;
                    for(var i = 0; i < data.length; i++){
                        _sumBudget += parseFloat(data[i].supplementary_budget);
                        _sumManDay += parseFloat(data[i].man_day);
                    }
                    if (_sumBudget && _sumBudget > 0) {_sumBudget = _sumBudget;}
                    else {_sumBudget = 0;}
                    if (_sumManDay && _sumManDay > 0) {_sumManDay = _sumManDay;}
                    else {_sumManDay = 0;}
                    $('#budget_custom p').html(_sumBudget + ' &euro;');
                    $('#man_day_custom p').html(_sumManDay + ' <?php __('M.D') ?>');
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
                return true;
            }
            if (_sumBudget && _sumBudget > 0) {_sumBudget = _sumBudget;}
            else {_sumBudget = 0;}
            if (_sumManDay && _sumManDay > 0) {_sumManDay = _sumManDay;}
            else {_sumManDay = 0;}
            _sumManDay = number_format(_sumManDay, 2, ',', ' ');
            _sumBudget = number_format(_sumBudget, 2, ',', ' ');
            // header =
                // '<div id = "wd-header-custom">'
                    // + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l1 r1 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l2 r2 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l3 r3 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l4 r4 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l5 r5 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l6 r6 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l7 r7 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l8 r8 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l9 r9 wd-row-custom wd-custom-cell"></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l10 r10 wd-row-custom wd-custom-cell" id = "budget_custom" style="width:130px !important;text-align:right !important;"><p>' +_sumBudget+ ' &euro;</p></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l11 r11 wd-row-custom wd-custom-cell" id = "man_day_custom" style="width:130px !important;text-align:right !important;"><p>' +_sumManDay+ ' <?php __('M.D') ?></p></div>'
                    // + '<div class="ui-state-default slick-headerrow-column l12 r12 wd-row-custom wd-custom-cell"></div>'
              // + '</div>';
            // $('.slick-header-columns').after(header);

            dataGrid.setSortColumns('weight' , true);

            //drag N drop 2014/20/12
            //added by QN

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
                    url : '<?php echo $html->url('/project_evolutions/order/' . $projectName['Project']['id']) ?>',
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
				$('.multiselect-filter input').val('').trigger('change');
				$('.multiSelectOptions input[type="checkbox"]').prop('checked', false).trigger('change');
				dataGrid.setSortColumn();
				$('input[name="project_container.SortOrder"]').val('').trigger('change');
				$('input[name="project_container.SortColumn"]').val('').trigger('change');
            }
    })(jQuery);
</script>
