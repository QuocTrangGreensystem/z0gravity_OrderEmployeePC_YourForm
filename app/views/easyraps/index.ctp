<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('multipleUpload/jquery.plupload.queue'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables');                    ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-input-select{
        margin-bottom: 3px;
    }
    .wd-input-select label{
        font-size: 13px;
        font-weight: bold;
        padding-right: 20px;
        margin-top: 12px;
        display: block;
        float: left;
    }
    .wd-input-select select{
        padding: 5px;
        float: left;
        border: 1px solid rgb(179, 179, 179);
    }
    .gr-settings{
        width: 500px;
    }
    .gr-settings .wd-input-select select{
        float: right;
    }
    .img{
        margin-left: 40%;
        vertical-align: middle;
    }
    .img-dialog:hover{
        cursor: pointer;
    }
</style>
<style>
    .select-display {
        width: 100%;
        padding: 3px 0;
    }
    .display td,
    .display th {
        vertical-align: middle;
    }
    .wd-overlay {
        position: relative;
    }
    .wd-overlay span {
        display: block;
        position: absolute;
        left: 45%;
        top: 10px;
        width: 16px;
        height: 16px;
        background: url(<?php echo $html->url('/img/ajax-loader.gif') ?>) no-repeat;
        display: none;
    }
    .index {
        cursor: move;
    }
    .display td {
        background: #fff;
    }
    .msg {
        position: fixed;
        top: 40%;
        left: 40%;
        width: 20%;
        background: #fff;
        padding: 10px;
        border: 10px solid #eee;
        border-radius: 6px;
        display: none;
        color: #000;
        text-align: center;
    }
    .unsortable .index {
        cursor: default;
    }
    .row-hidden td {
        background: #ffc;
    }
    .plupload_droptext{
        color: #000;
    }
    .plupload_filelist{
        max-height: 100px;
        color: #000;
    }
    .plupload_file_action a{
        margin-right: -100px;
        float: right;
    }
    .plupload_filelist .plupload_file_name{
        width: 31%;
    }
    .plupload_file_size, .plupload_file_status, .plupload_progress{
        width: 130px;
    }
    .plupload_content{
        height: 160px;
    }
    li.plupload_droptext{
        line-height: 50px;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-title">
                <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" title="<?php __('Import CSV file')?>"></a>
                <a href="<?php echo $this->Html->url(array('action' => 'export'));?>" id="export-table" class="btn btn-excel"></a>
                <a href="javascript:void(0);" class="btn btn-plus-green" id="new-internal" onclick="addEasyrap();" title="<?php __('Add an new easyraps') ?>"></a>
            </div>
            <div class="wd-list-project">
                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;clear: both;">

                                </div>
                                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php
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
    'slick_grid/plugins/slick.dataexporter',
    'multipleUpload/plupload.full.min',
    'multipleUpload/jquery.plupload.queue',
    'jquery.ui.touch-punch.min'
));
?>
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
        'id' => 'date_operation',
        'field' => 'date_operation',
        'name' => __('Date of operation', true),
        'width' => 300,
		'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'editor' => 'Slick.Editors.datePicker',
		'formatter' => 'Slick.Formatters.nameSales'
    ),
	array(
        'id' => 'date_value',
        'field' => 'date_value',
        'name' => __('Date of value', true),
        'width' => 300,
		'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'editor' => 'Slick.Editors.datePicker',
		'formatter' => 'Slick.Formatters.nameSales'
    ),
    array(
        'id' => 'amount',
        'field' => 'amount',
        'name' => __('Amount', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValueBudget',
		'formatter' => 'Slick.Formatters.erroValue',
    ),
    array(
        'id' => 'label',
        'field' => 'label',
        'name' => __('Label', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
	array(
        'id' => 'balances',
        'field' => 'balances',
        'name' => __('Balances', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.numericValueBudget',
		'formatter' => 'Slick.Formatters.erroValue',
    ),
	array(
        'id' => 'purchase_journal',
        'field' => 'purchase_journal',
        'name' => __('Purchase Jounrnal', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'ignoreExport' => true,
        'editor' => 'Slick.Editors.livrableAttachment',
        'formatter' => 'Slick.Formatters.livrableAttachment'
    ),
	array(
        'id' => 'customer',
        'field' => 'customer',
        'name' => __('Compte', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.selectBoxFormatter'
    ),
	array(
        'id' => 'category',
        'field' => 'category',
        'name' => __('Category', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 100,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'ignoreExport' => true,
        'formatter' => 'Slick.Formatters.Action'
        ));
$dataView = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();
foreach ($easyraps as $easyrap) {
    $data = array(
        'id' => $easyrap['Easyrap']['id'],
        'company_id' => $company_id,
        'no.' => $easyrap['Easyrap']['id'],
        'MetaData' => array()
    );
    $data['date_operation'] = $easyrap['Easyrap']['date_operation'] ? $str_utility->convertToVNDate($easyrap['Easyrap']['date_operation']) : '';
    $data['date_value'] = $easyrap['Easyrap']['date_value'] ? $str_utility->convertToVNDate($easyrap['Easyrap']['date_value']) : '';
	$data['company_id'] = $easyrap['Easyrap']['company_id'];
	$data['amount'] = (string) $easyrap['Easyrap']['amount'];
	$data['label'] = (string) $easyrap['Easyrap']['label'];
	$data['balances'] = (string) $easyrap['Easyrap']['balances'];
	$data['purchase_journal'] = $easyrap['Easyrap']['purchase_journal'];
    $data['format'] = (string) $easyrap['Easyrap']['format'];
	$data['customer'] = !empty($saleNames[ $easyrap['Easyrap']['customer_id'] ]) ? $saleNames[ $easyrap['Easyrap']['customer_id'] ] : '';
	$data['category'] = !empty($category[$easyrap['Easyrap']['category_id']]) ? $category[$easyrap['Easyrap']['category_id']] : '';

    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
$selectMaps = array(
    'category' => $category
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
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <div class="wd-scroll-form" style="height:auto;">
            <hr style="clear: both;" />
            <div class="wd-right-content">
                <div class="wd-input wd-calendar" rels="<?php echo Configure::read('Config.language'); ?>" id="languageTranslationAudit">
                    <label for="attachments" style="width: 40px;"></label>
                    <p style="color: #F00808; font-size: 13px; font-style: italic; margin-left: 5px"><?php echo __('"Add files" and then "Start Upload"', true);?></p>
                </div>
                <div id="uploaderOrder" class="wd-input wd-calendar" style="margin-top: -10px;">
                    <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<!-- dialog_attachement_or_url.end -->
<div id="dialog_import_CSV" style="display:none" title="<?php __('Import CSV file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'easyraps', 'action' => 'import_csv')));
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
<script type="text/javascript">
    var DataValidator = {};
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
	var category = <?php echo json_encode($category); ?>;
    var saleNames = <?php echo json_encode($saleNames); ?>;
    var saleTypes = <?php echo json_encode($saleTypes); ?>;
    var ControlGrid;

    function updateCustomer(id, cell){
        var t = $(this).prop('disabled', true).css('background-color', '#eee');
        var v = $(this).val();
        //ajax
        $.ajax({
            url: '<?php echo $this->Html->url('/easyraps/updateCustomer') ?>',
            type: 'POST',
            data: {
                id: id,
                customer_id: v
            },
            complete: function(){
                var dataView = ControlGrid.getDataView();
                //update grid
                var row = ControlGrid.getData().getRowById(id);
                var item = ControlGrid.getData().getItem(row);
                dataView.beginUpdate();
                item['customer'] = saleNames[v] ? saleNames[v] : '';
                dataView.updateItem(id, item);
                dataView.endUpdate();
                t.prop('disabled', false).css('background-color', '#fff');
            }
        });
    }
    // upload

    // import csv
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
    $("#action-attach-url").live('click',function(){
        createDialog();
        var titlePopup = <?php echo json_encode(__('Attachement', true))?>;
        $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
    });
    var createDialog = function(){
        $('#dialog_attachement_or_url').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 700,
            open : function(e){
                var $dialog = $(e.target);
                $dialog.dialog({open: $.noop});
            }
        });
        createDialog = $.noop;
    }
    $('.plupload_start').live('click', function(){
        setTimeout(function(){
            $("#dialog_attachement_or_url").dialog('close');
            location.reload(true);
        }, 3000);
    });
    // number_format
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
    }
    (function($){

        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html(),
                attachmentTemplate =  $('#attachment-template').html();

            $.extend(Slick.Formatters,{
				nameSales : function(row, cell, value, columnDef, dataContext){
					var date = value.split('-');
					if(date[0].length == 4){
						value = date[2] + '-' + date[1] + '-' + date[0];
					}
                    return '<span class="row-disabled">' + value + '</span>';
                },
				erroValue : function(row, cell, value, columnDef, dataContext){
					value = value ? number_format(parseFloat(value), 2, ',', ' ') + ' €' : '0.00 €';
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-parent row-number">' + value + '</span>', columnDef, dataContext);
        		},
				selectBoxFormatter : function(row, cell, value, columnDef, dataContext){
                    var html = '<select style="min-width: 190px; padding: 5px 10px" rel="no-history"  onchange="updateCustomer.call(this, \'' + dataContext.id + '\', ' + cell + ')"><option value="0">--Any--</option>';
                    for(var i in saleNames){
                        var style = '';
                        if(dataContext['amount'] < 0){
                            if(saleTypes[i] == 'cus' && saleNames[i] != dataContext['customer']){
                                style = 'style = "display: none"';
                            }
                        } else {
                            if(saleTypes[i] == 'pro' && saleNames[i] != dataContext['customer']){
                                style = 'style = "display: none"';
                            }
                        }
                        html += '<option value="' + i + '" '+ style + (saleNames[i] == dataContext['customer'] ? 'selected' : '') + '>' + saleNames[i] + '</option>';
                    }
                    html += '</select>';
	                return Slick.Formatters.HTMLData(row, cell, html, columnDef, dataContext);
	            },
                livrableAttachment : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        value = $this.t(attachmentTemplate,dataContext.id,row);
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    var _html = '';
                    _html = $this.t(actionTemplate,dataContext.id, dataContext.company_id,dataContext.name);
                    return Slick.Formatters.HTMLData(row, cell, _html, columnDef, dataContext);
                }
            });
			$.extend(Slick.Editors,{
                numericValueBudget : function(args){
        			$.extend(this, new Slick.Editors.textBox(args));
        			this.input.attr('maxlength' , 10).keypress(function(e){
        				var key = e.keyCode ? e.keyCode : e.which;
        				if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
        					return;
        				}
        				var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        ///^[\-+]??$/
                        //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
        				if(!/^[\-]?([0-9]{0,9})(\.[0-9]{0,2})?$/.test(val)){
        					e.preventDefault();
        					return false;
        				}
        			});
        		},
                livrableAttachment : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
        			this.input = $("<a href='#' id='action-attach-url'></a><div class='browse'></div>")
        			.appendTo(args.container).attr('rel','no-history').addClass('editor-text');
                    var id = args.item.id;
                    var uploader = $("#uploaderOrder").pluploadQueue({
                        runtimes : 'html5, html4',
                        url : "/easyraps/update_document/"+id,
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
                                up.idOfLead = id;
                                up.company_id = args.item.company_id;
                                up.linkedAction = "/easyraps/update_document/"+id;
                                up.singleUpload = 1;
                            }
                        }
                    });
                    this.focus();
                }
            });
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                date_operation : {defaulValue : '' , maxLength: 100},
                date_value : {defaulValue : '', maxLength : 100},
				amount : {defaulValue : '', maxLength : 100},
				label : {defaulValue : '' , maxLength: 200},
                balances : {defaulValue : '', maxLength : 100},
				purchase_journal : {defaulValue : '', maxLength : 255},
				customer_id : {defaulValue : '' , maxLength: 100},
                category_id : {defaulValue : '', maxLength : 100}
            };
            $this.onBeforeEdit = function(args){
                if(args.column.field == 'purchase_journal' && (!args.item || args.item['purchase_journal'] || !args.item['id'])){
                    return false;
                }
                return true;
            }
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            ControlGrid = $this.init($('#project_container'),data,columns);
            addEasyrap = function(){
                ControlGrid.gotoCell(data.length, 1, true);
            }
            var exporter = new Slick.DataExporter('/easyraps/export');
            ControlGrid.registerPlugin(exporter);

            $('#export-table').click(function(){
                exporter.submit();
                return false;
            });
            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = ControlGrid.getDataItem(row);
                if(data && confirm($this.t('Are you sure you want to delete attachment : %s'
                , data['purchase_journal']))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['purchase_journal'] = '';

                    ControlGrid.updateRow(row);
                }
                return false;
            });
        });
    })(jQuery);
</script>
