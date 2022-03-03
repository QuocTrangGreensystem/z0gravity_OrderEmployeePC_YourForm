<?php echo $html->css(array('jquery.multiSelect','slick_grid/slick.grid','slick_grid/slick.pager','slick_grid/slick.common','slick_grid/slick.edit')); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style type="text/css">
.ui-datepicker-year{
    display:none;
}
.wd-input-contries{
    margin-left: 46px;
    margin-top: -55px;
    width: auto;
    height: 32px;
    margin-bottom: 30px;
    display: inherit;
}
</style>
<!-- /export excel  -->
<!-- dialog_import -->
<div id="dialog_import_CSV" title="<?php __('Import CSV file') ?>" class="buttons" style="display: none;">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'absences', 'action' => 'import_csv')));
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
<!-- End dialog_import -->
<!-- dialog_data_csv -->
<div id="dialog_data_CSV" title="Data CSV file" class="buttons">

</div>
<!-- End dialog_data_csv -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3">&nbsp;</h2>
                                <a href="javascript:void(0)" class="import-excel-icon-all" id="import_CSV" style="margin-right:5px; margin-top: -38px;" title="<?php __('Import CSV file')?>"><span><?php __('Import CSV') ?></span></a>
                                <?php if($mutil_country): ?>
                                <select class="wd-input-contries" name="typeRequest" id="typeRequest">
                                    <?php foreach ($list_country as $id => $name) { ?>
                                        <option value="<?php echo $id ?>" <?php echo $typeSelect == $id ?'selected' : '';?>><?php echo $name?></option>
                                    <?php } ?>
                                </select>
                                <?php endif; ?>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

                                </div>
                                <!--<div id="pager" style="width:100%;height:36px; overflow: hidden;">

                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php echo $html->script('slick_grid/slick.editors'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script(array('slick_grid_custom')); ?>
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
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'recursive',
        'field' => 'recursive',
        'name' => __('Recursive', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'code1',
        'field' => 'code1',
        'name' => __('Code 1', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DateValidate.isUnique',
    ),
    array(
        'id' => 'code2',
        'field' => 'code2',
        'name' => __('Code 2', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        //'validator' => 'DateValidate.isUnique',
    ),
    array(
        'id' => 'code3',
        'field' => 'code3',
        'name' => __('Code 3', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        //'validator' => 'DateValidate.isUnique',
    ),
    array(
        'id' => 'type',
        'field' => 'type',
        'name' => __('Type', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'print',
        'field' => 'print',
        'name' => __('Print', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textPrint'
    ),
    array(
        'id' => 'total',
        'field' => 'total',
        'name' => __('Number by year', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
    ),
    array(
        'id' => 'begin',
        'field' => 'begin',
        'name' => __('Begin of period', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        //'editor' => 'Slick.Editors.dateAbsencePicker',
        'formatter' => 'Slick.Formatters.beginOfPeriod',
        'validator' => 'DateValidate.beginOfPeriod',
    ),
    array(
        'id' => 'end_of_period',
        'field' => 'end_of_period',
        'name' => __('End of period', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.datePicker',
        //'editor' => 'Slick.Editors.dateAbsencePicker',
        'formatter' => 'Slick.Formatters.endOfPeriod',
        'validator' => 'DateValidate.endOfPeriod',
    ),
    array(
        'id' => 'activated',
        'field' => 'activated',
        'name' => __('Activated', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'display',
        'field' => 'display',
        'name' => __('Display', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'document',
        'field' => 'document',
        'name' => __('Document', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'weight',
        'field' => 'weight',
        'name' => __('Order', true),
        'width' => 60,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Order'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array(
    'activated' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'display' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'recursive' => array('yes' => __('Yes', true), 'no' => __('No', true)),
    'document' => array('yes' => __('Yes', true), 'no' => __('No', true))
);

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($absences as $absence) {
    $data = array(
        'id' => $absence['Absence']['id'],
        'company_id' => $company_id,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $absence['Absence']['name'];
    $data['code1'] = (string) $absence['Absence']['code1'];
    $data['code2'] = (string) $absence['Absence']['code2'];
    $data['code3'] = (string) $absence['Absence']['code3'];
    $data['type'] = (string) $absence['Absence']['type'];
    $data['print'] = (string) $absence['Absence']['print'];
    $data['total'] = (string) $absence['Absence']['total'];
    $data['begin'] = $str_utility->convertToVNDate($absence['Absence']['begin']);
    $data['end_of_period'] = $str_utility->convertToVNDate($absence['Absence']['end_of_period']);
    //$data['total'] = !empty($absenceHistories[$absence['Absence']['id']]['total']) ? $absenceHistories[$absence['Absence']['id']]['total'] : '';
    //$data['begin'] = !empty($absenceHistories[$absence['Absence']['id']]['begin']) ? $str_utility->convertToVNDate($absenceHistories[$absence['Absence']['id']]['begin']) : '';
    $data['activated'] = $absence['Absence']['activated'] ? 'yes' : 'no';
    $data['display'] = $absence['Absence']['display'] ? 'yes' : 'no';
    $data['recursive'] = $absence['Absence']['recursive'] ? 'yes' : 'no';
    $data['document'] = $absence['Absence']['document'] ? 'yes' : 'no';
    $data['weight'] = (int) $absence['Absence']['weight'];

    $data['action.'] = '';

    $dataView[] = $data;
}

$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
$startDates = '01-'.date('m-Y', $_start);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="order-template" style="display: none;">
    <div style="display: block;padding-top: 6px;" class="phase-order-handler overlay" rel="%s">
        <span class="wd-up" style="cursor: pointer;"></span>
        <span class="wd-down" style="cursor: pointer;"></span>
    </div>
</div>
<script type="text/javascript">

	var wdTable = $('.wd-table');
	$(document).ready(set_slick_table_height);
	$(window).resize(set_slick_table_height);
	function set_slick_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 45;
			console.log(heightTable);
			wdTable.css({
				height: heightTable,
			});
			heightViewPort = heightTable - 72;
			wdTable.find('.slick-viewport').height(heightViewPort);
			console.log( heightViewPort, "   ");
			clearInterval(wdTable);
		}
	}

    var DateValidate = {};
    (function($){

        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date
            var actionTemplate =  $('#action-template').html();
            var orderTemplate = $('#order-template').html();
            DateValidate.isUnique = function(value,args){
                var result = true,_value = $.trim(value).toLowerCase();
                $.each(args.grid.getData().getItems() , function(undefined,dx){
                    //console.log(args.item.id, dx.id);
                    if(args.item.id && args.item.id == dx.id){
                        if(_value == $.trim(args.item[args.column.field =='code1' ? 'code2' : 'code1']).toLowerCase()){
                            return (result = false);
                        }
                        return true;
                    }
                    return (result = (dx.code1.toLowerCase() != _value && dx.code2.toLowerCase() != _value) );
                });
                return {
                    valid : result,
                    message : $this.t('The code is avaiable, please enter another!')
                };
            }
            var start = <?php echo json_encode($startDates);?>;
            DateValidate.beginOfPeriod = function(value, args, trigger){
                if(args.item.end_of_period == ''){
                    return {
                        valid : true,
                        message : $this.t('')
                    };
                }
                var begin = $this.getTime(value), end = $this.getTime(args.item.end_of_period);
                return {
                    valid : begin < end,
                    message : $this.t('The start of period must be smaller than end of period')
                };
            };
            DateValidate.endOfPeriod = function(value, args, trigger){
                if(args.item.begin == ''){
                    return {
                        valid : true,
                        message : $this.t('')
                    };
                }
                var begin = $this.getTime(args.item.begin), end = $this.getTime(value);
                return {
                    valid : begin < end,
                    message : $this.t('The end of period must be greater than start of period')
                };
            };
            $this.onBeforeSave = function(args){
                if(args.item.total && !args.item.begin){
                    args.item.MetaData.cssClasses = 'pendding';
                    $(args.grid.getCellNode(args.row,args.cell)).parent()
                    .removeClass('error pendding success disabled').addClass('pendding');
                    return false;
                }
            };

            $.extend(Slick.Formatters,{
                beginOfPeriod : function(row, cell, value, columnDef, dataContext){
                    //var months = {'01': 'Jan','02':'Fed','03':'Mar','04':'Apr','05':'May','06':'Jun','07':'Jul','08':'Aug','09':'Sep','10':'Oct','11':'Nov','12':'Dec'};
//                    if(value && value != 'null'){
//                        var valueN = value.substr(0, 5);
//                        var valueC = valueN.split('-');
//                        value = valueC[0]+'-'+months[valueC[1]];
//                    }
                    if(value != ''){
                        value = (value != 0) ? value.split('-') : '';
                        if(dataContext.recursive == 'no'){
                            var year = value[2].substring(2, 4);
                            value = value[0] + '-' + value[1] + '-' + year;
                        } else {
                            value = value[0] + '-' + value[1];
                        }
                    }

                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                endOfPeriod : function(row, cell, value, columnDef, dataContext){
                    //var months = {'01': 'Jan','02':'Fed','03':'Mar','04':'Apr','05':'May','06':'Jun','07':'Jul','08':'Aug','09':'Sep','10':'Oct','11':'Nov','12':'Dec'};
//                    if(value && value != 'null'){
//                        var valueN = value.substr(0, 5);
//                        var valueC = valueN.split('-');
//                        value = valueC[0]+'-'+months[valueC[1]];
//                    }
                    if(value != ''){
                        value = (value != 0) ? value.split('-') : '';
                        if(dataContext.recursive == 'no'){
                            var year = value[2].substring(2, 4);
                            value = value[0] + '-' + value[1] + '-' + year;
                        } else {
                            value = value[0] + '-' + value[1];
                        }
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.company_id,dataContext.name), columnDef, dataContext);
                },
                Order : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(orderTemplate , row));
                }
            });
            $.extend(Slick.Editors, {
                textPrint:  function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 20);
                }
            });
            $this.onCellChange = function(args){
                args.item.weight = args.item.weight ? args.item.weight : data.length;
                if(args.item && args.item.recursive == 'no' && ((args.item.begin != '' && args.item.end_of_period == '') || (args.item.begin == '' && args.item.end_of_period != ''))){
                    return false;
                }
            };

            $this.onBeforeEdit = function(args){
                if(args.item && args.item.recursive == 'yes' && args.column.field == 'end_of_period'){
                    return false;
                }
                return true;
            };

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var typeSelect = <?php echo json_encode($typeSelect) ?>;
            var mutil_country = <?php echo json_encode($mutil_country) ?>;
            typeSelect = mutil_country ? typeSelect : '';
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false},
                code1 : {defaulValue : ''},
                code2 : {defaulValue : ''},
                code3 : {defaulValue : ''},
                type : {defaulValue : '',allowEmpty : false},
                print : {defaulValue : '',allowEmpty : false},
                total : {defaulValue : ''},
                //begin : {defaulValue : '' , required : ['total']},
                begin : {defaulValue : ''},
                end_of_period : {defaulValue : ''},
                activated : {defaulValue : ''},
                display : {defaulValue : ''},
                recursive : {defaulValue : ''},
                document : {defaulValue : ''},
                weight : {defaulValue : 0 },
                country_id : {defaulValue : typeSelect}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns);

            dataGrid.setSortColumn('weight' , true);
            $('[name="project_container.SortOrder"],[name="project_container.SortColumn"]').remove();
            var $colSort = $(dataGrid.getHeaderRow()).closest('.ui-widget').find('.slick-header-column-sorted .slick-column-name');
            var sortData = function(){
                $.each(dataGrid.getSortColumns() , function(){
                    this.sortAsc = !this.sortAsc;
                });
                $colSort.data('autoTrigger', true)
                $colSort.click();
            };
            $colSort.click(function(e){
                if(!$colSort.data('autoTrigger')){
                    e.stopImmediatePropagation();
                    return false;
                }
                $colSort.data('autoTrigger',false);
            });
            //
            (function(){

                if(!$this.canModified){
                    return;
                }
                var saveData = {},timeoutID;

                var updateSort = function(){
                    $.ajax({
                        url : '<?php echo $html->url(array('action' => 'order', $company_id)); ?>',
                        cache : false,
                        type : 'POST',
                        data : {
                            data : $.extend({},saveData)
                        }
                    });
                    saveData = {};
                };

                var getRow = function(row){
                    var $el = $(dataGrid.getCellNode(row, 1));
                    if($el.length){
                        return $el.parent();
                    }
                    return false;
                };

                var  toggleElement = function(s , d){
                    var sdata = dataGrid.getDataItem(s);
                    var ddata = dataGrid.getDataItem(d);

                    var w = ddata.weight;
                    ddata.weight = sdata.weight;
                    sdata.weight = w;

                    saveData[sdata.id] = sdata.weight;
                    saveData[ddata.id] = ddata.weight;

                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(updateSort , 1500);
                    sortData();
                    var $s = getRow(d);
                    $s.stop().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                        $s.animate({backgroundColor:'#FFF'}, 'slow' , function(){
                            $s.css('backgroundColor' , '');
                        });
                    });
                };

                $('.phase-order-handler span.wd-up').live('click' , function(){
                    var row = Number($(this).parent().attr('rel'));
                    if (getRow(row - 1)) {
                        toggleElement(row,row - 1);
                    }
                });

                $('.phase-order-handler span.wd-down').live('click' , function(){
                    var row = Number($(this).parent().attr('rel'));
                    if (getRow(row + 1)) {
                        toggleElement(row,row + 1);
                    }
                });

            })();

        });

        $('#dialog_import_CSV').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 360,
                height      : 125
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
                }else{
                    jQuery('<div>', {
                        'class': 'error-message',
                        text: 'Please choose a file!'
                    }).appendTo('#error');
                }
            });

            $(".cancel").live('click',function(){
                $("#dialog_data_CSV").dialog("close");
                $("#dialog_import_CSV").dialog("close");
            });
            $('#typeRequest').change(function(){
                var linkRequest = '/absences/index/',
                    company_id = <?php echo json_encode($company_id) ?>,
                    country_id = $(this).val();
                linkRequest = linkRequest + company_id + '/' + country_id;
                window.location.href = linkRequest;
            });

    })(jQuery);
</script>
