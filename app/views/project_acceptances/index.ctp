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
    .row-center-custom{
        text-align: center;
    }
    .row-date{
        text-align: center;
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
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="%2$s"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="weather-template" style="display: none;">
    <div style="overflow: hidden;">
        <div class="wd-input wd-weather-list-dd">
            <ul style="float: left; display: inline; overflow: hidden">
                <li><input style="margin-top: 8px" value="sun" name="weather-%1$s" data-id="%1$s" %2$s class="weather weather-sun" type="radio"> <img style="float: none" title="Sun" src="<?php echo $this->Html->url('/') ?>img/sun.png"></li>
                <li><input type="radio" value="cloud" name="weather-%1$s" class="weather weather-cloud" %3$s style="margin-top: 8px;" data-id="%1$s"> <img style="float: none" title="Cloud" src="<?php echo $this->Html->url('/') ?>img/cloud.png"></li>
                <li><input type="radio" value="rain" name="weather-%1$s" class="weather weather-rain" %4$s style="margin-top: 8px;" data-id="%1$s"> <img style="float: none" title="Rain" src="<?php echo $this->Html->url('/') ?>img/rain.png"></li>
            </ul>
        </div>
    </div>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url',
                'url' => array('controller' => 'project_acceptances', 'action' => 'upload')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachement") ?></label>
                <p id="gs-attach"></p>
                <?php
                echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                echo $this->Form->hidden('project_id', array('value' => $project_id, 'rel' => 'no-history'));
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

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="add-new-sales" style="margin-right:5px;" onclick="addNewAcceptanceButton();" title="<?php __('Add an item') ?>"></a>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:400px;"></div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;"></div>
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
        'id' => 'project_acceptance_type_id',
        'field' => 'project_acceptance_type_id',
        'name' => __('Acceptance type', true),
        'width' => 200,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        'validator' => 'DateValidate.isUnique'
    ),
    array(
        'id' => 'weather',
        'field' => 'weather',
        'name' => __('Weather', true),
        'width' => 250,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.weather'
    ),
    array(
        'id' => 'progress',
        'field' => 'progress',
        'name' => __('Progress', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.forecastValue',
        'formatter' => 'Slick.Formatters.numberValPercent'
    ),
    array(
        'id' => 'due_date',
        'field' => 'due_date',
        'name' => __('Due date', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.dateValue'
    ),
    array(
        'id' => 'effective_date',
        'field' => 'effective_date',
        'name' => __('Effective date', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.dateValue'
    ),
    array(
        'id' => 'employee_id',
        'field' => 'employee_id',
        'name' => __('Manager', true),
        'width' => 200,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
    ),
    array(
        'id' => 'file',
        'field' => 'file',
        'name' => __('Attachement or URL', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => false,
        'resizable' => false,
        'editor' => 'Slick.Editors.Attachement',
        'formatter' => 'Slick.Formatters.Attachement'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __(' ', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
$dataView = array();
App::import('Vendor', 'str_utility');
foreach ($acceptances as $acceptance) {
    $data['id'] = $acceptance['ProjectAcceptance']['id'];
    $data['project_acceptance_type_id'] = $acceptance['ProjectAcceptance']['project_acceptance_type_id'];

    $data['weather'] = $acceptance['ProjectAcceptance']['weather'];
    if( !$data['weather'] )$data['weather'] = 'sun';
    //$data['name'] = $acceptance['ProjectAcceptanceType']['name'];
    $data['progress'] = floatval($acceptance['ProjectAcceptance']['progress']);

    $data['due_date'] = $acceptance['ProjectAcceptance']['due_date'] ? str_utility::convertToVNDate($acceptance['ProjectAcceptance']['due_date']) : '';
    $data['effective_date'] = $acceptance['ProjectAcceptance']['effective_date'] ? str_utility::convertToVNDate($acceptance['ProjectAcceptance']['effective_date']) : '';

    $data['employee_id'] = $acceptance['ProjectAcceptance']['employee_id'];

    $data['file'] = $acceptance['ProjectAcceptance']['file'];

    $data['action.'] = '';
    $data['format'] = $acceptance['ProjectAcceptance']['file_type'];
    $data['project_id'] = $project_id;
    $dataView[] = $data;
}

$selectMaps = array(
    'employee_id' => $employees,
    'project_acceptance_type_id' => $types
);

$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Date closing must between %1$s and %2$s' => __('Date closing must between %1$s and %2$s', true),
    'Delete?' => __('Delete?', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want to delete this acceptance?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 550) ? 550 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 550) ? 550 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
    var DateValidate = {},ControlGrid,IuploadComplete = function(json){
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
    var projectName = <?php echo json_encode($projectName['Project']); ?>;
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
    (function($){

        $(function(){

            var $this = SlickGridCustom;


            DateValidate.isUnique = function(value,args){
                var result = true,_value = $.trim(value).toLowerCase();
                var items = args.grid.getData().getItems();
                for(var i in items){
                    if( items[i].project_acceptance_type_id == _value ){
                        result = false;
                        break;
                    }
                }
                return {
                    valid : result,
                    message : $this.t('This item already existed!')
                };
            };

            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = ControlGrid.getDataItem(row);
                if(data && confirm($this.t('Delete?')) ){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['file'] = '';
                    ControlGrid.updateRow(row);
                }
                return false;
            });

            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode((!empty($canModified) && !$_isProfile )|| ($_isProfile && $_canWrite)); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps) ?>;
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
            attachmentURLTemplate =  $('#attachment-template-url').html(),
            weatherTemplate =  $('#weather-template').html();

            $.extend(Slick.Formatters,{
                weather: function(row, cell, value, columnDef, dataContext){
                    var current = dataContext.weather;
                    switch(current){
                        case 'sun':
                            value = $this.t(weatherTemplate, dataContext.id, 'checked', ' ', ' ', row);
                            break;
                        case 'cloud':
                            value = $this.t(weatherTemplate, dataContext.id, ' ', 'checked', ' ', row);
                            break;
                        case 'rain':
                            value = $this.t(weatherTemplate, dataContext.id, ' ', ' ', 'checked', row);
                            break;
                        default:
                            value = $this.t(weatherTemplate, dataContext.id, ' ', ' ', ' ', row);
                            break;
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                numberValPercent: function(row, cell, value, columnDef, dataContext){
                    value = value ? value : 0;
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + number_format(value, 2, ',', ' ') + ' ' +'%'+ '</span> ', columnDef, dataContext);
                },
                dateValue: function(row, cell, value, columnDef, dataContext){
                    value = value ? value : '';
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-center">' + value + '</span> ', columnDef, dataContext);
                },
                Attachement : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 1){
                            value = $this.t(attachmentTemplate,dataContext.id,row);
                        }
                        if(dataContext.format == 0){
                            value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.file,row);
                        }
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.project_issue_problem), columnDef, dataContext);
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

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                weather : {defaulValue : ''},
                progress : {defaulValue : 0},
                employee_id : {defaulValue : null},
                due_date : {defaulValue : ''},
                effective_date : {defaulValue : ''},
                file : {defaulValue : ''},
                format : {defaulValue : -1},
                file : {defaulValue : ''},
                project_id: {defaulValue : <?php echo $project_id ?>},
                project_acceptance_type_id: {defaulValue: 0}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns, {
				frozenColumn: 0,
				rowHeight: 40,
				headerRowHeight: 40, 
			});
            $this.onBeforeEdit = function(args){
                if( !args.item && args.column.field != 'project_acceptance_type_id' ){
                    return false;
                }
                // if( args.column.field == 'project_acceptance_type_id' && args.item ){
                // 	return false;
                // }
                return true;
            }
            $this.onCellChange = function(args){
                $('.row-center').parent().addClass('row-center-custom');
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
            // add new colum grid
            //ControlGrid1 = $this.init($('#project_container'),data,columns);
            addNewAcceptanceButton = function(){
                ControlGrid.gotoCell(data.length, 0, true);
            }
            $('.row-center').parent().addClass('row-center-custom');
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
        //weather
        $(document).on('click', '.weather', function(e){
            var t = $(this),
                newWeather = t.val(),
                id = '' + t.data('id'),
                dataView = ControlGrid.getDataView(),
                item = dataView.getItemById(id);
            if( item['weather'] != newWeather ){
                item['weather'] = newWeather;
                dataView.updateItem(id, item);
                ControlGrid.render();
                t.parent().children('.weather').prop('disabled', true);
                $.ajax({
                    url: '<?php echo $this->Html->url('/') ?>project_acceptances/updateWeather',
                    type: 'POST',
                    data: {
                        data: {
                            id : t.data('id'),
                            project_id: <?php echo $project_id ?>,
                            weather: newWeather
                        }
                    },
                    complete: function(){
                        setTimeout(function(){
                            t.parent().children('.weather').prop('disabled', false);
                        }, 500);
                    }
                });
            }
        });

    })(jQuery);
</script>
