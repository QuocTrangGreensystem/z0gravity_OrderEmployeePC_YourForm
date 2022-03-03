<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>

<style>
#table-control .input{display:block; padding-left:0}.export-excel-icon-all{float: left;}#table-control{float:left;}
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
.wd-tab .wd-panel{
	padding: 20px;
	border: none;
}
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'employee_absences', 'action' => 'export', $employee_id, $company_id)));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title" style="margin-bottom: 3px !important; padding-top: 5px; margin-top: -12px;">
                   <!-- <h2 class="wd-t1"><?php echo sprintf(__("Absences of %s %s", true), $employeeName['Employee']['first_name'] , $employeeName['Employee']['last_name']); ?></h2> -->
                    <div id="table-control" class="wd-activity-actions">
                        <?php
                        echo $this->Form->create('Control', array(
                            'type' => 'get',
                            'url' => '/' . Router::normalize($this->here)));
                        ?>
                        <fieldset>
                            <!-- <h3 class="input"><?php __('You are view in :'); ?></h3> -->
                            <div class="input" style="float: left;">
                                <?php
                                    $_start = isset($start) ? $start : date('Y', $_start);
                                    echo $this->Form->year('year', $_start - 5, $_start + 2, $_start, array('empty' => false));
                                ?>
                            </div>
                            <?php /*
                            <div class="input" style="float: left; border: 1px solid #999; margin-left: 5px;">
                                <?php
                                    echo $this->Form->month('month', date('m', $_startCheck), array('empty' => false));
                                ?>
                            </div>
                            */ ?>
                            <button type="submit" class="button">
                            </button>
							<a href="javascript:void(0)" id="export-submit" class="export-excel-icon-all" title="<?php __('Export Excel')?>"><span><?php __('Export Excel'); ?></span></a>
                            <div style="clear:both;"></div>
                        </fieldset>
                        <?php
                        echo $this->Form->end();
                        ?>
                    </div>
                     
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:400px;">

                </div>
                <div id="pager" style="width:100%;height:0; overflow: hidden;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
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
        'resizable' => true
    ),
    array(
        'id' => 'code1',
        'field' => 'code1',
        'name' => __('Code 1', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'code2',
        'field' => 'code2',
        'name' => __('Code 2', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'code3',
        'field' => 'code3',
        'name' => __('Code 3', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'type',
        'field' => 'type',
        'name' => __('Type', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'print',
        'field' => 'print',
        'name' => __('Print', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'total',
        'field' => 'total',
        'name' => __('Number by year', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'begin',
        'field' => 'begin',
        'name' => __('Begin of period', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        //'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.ofPeriod',
        //'validator' => 'DateValidate.startDate'
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
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();


foreach ($absences as $absence) {
    $data = array(
        'id' => $absence['Absence']['id'],
        'company_id' => $company_id,
        'employee_id' => $employee_id,
        'year' => $start,
        'no.' => $i++,
        'MetaData' => array()
    );
    $data['name'] = (string) $absence['Absence']['name'];
    $data['code1'] = (string) $absence['Absence']['code1'];
    $data['code2'] = (string) $absence['Absence']['code2'];
    $data['code3'] = (string) $absence['Absence']['code3'];
    $data['type'] = (string) $absence['Absence']['type'];
    $data['print'] = (string) $absence['Absence']['print'];
    $data['begin'] = $str_utility->convertToVNDate($absence['Absence']['begin']);
    $data['total'] = (string) !empty($employeeAbsences[$data['id']]['total']) || (!empty($employeeAbsences[$data['id']]) && $employeeAbsences[$data['id']]['total'] == 0) ? $employeeAbsences[$data['id']]['total'] : $absence['Absence']['total'];
    $data['action.'] = '';

    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Are you sure you want set "%s" on default ?', true), '%4$s')); ?>');" class="wd-update" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s','%3$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('.wd-table');
var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 500) ? 500 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
    var DateValidate = {};
    (function($){
        var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  <?php echo json_encode($canModified); ?>;
        // For validate date
        var actionTemplate =  $('#action-template').html();

        $this.onCellChange = function(args){
            if(!args.item.total || !args.item.begin){
                args.item.MetaData.cssClasses = 'pendding';
                $(args.grid.getCellNode(args.row,args.cell)).parent()
                .removeClass('error pendding success disabled').addClass('pendding');
                return false;
            }
        };

        $.extend(Slick.Formatters,{
            ofPeriod : function(row, cell, value, columnDef, dataContext){
                if(value && value != 'null'){
                    value = $.datepicker.formatDate('dd-M', $.datepicker.parseDate($this.dateFormat,value) );
                }
                return Slick.Formatters.HTMLData(row, cell,value, columnDef, dataContext);
            },
            Action : function(row, cell, value, columnDef, dataContext){
                return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,dataContext.employee_id,
                dataContext.company_id,dataContext.name), columnDef, dataContext);
            }
        });

        DateValidate.startDate = function(value, args){
            var _absenceId = args.item.id;
            var _employee = args.item.employee_id;
            var _year = args.item.year;
            function requestAbsence() {
                var _id = args.item.id;
                var href = "/employee_absences/checkAbsence/" +_id+ "/" +_employee+ "/" +_year;
                var result="";
                $.ajax({
                  url: href,
                  async: false,
                  dataType: 'json',
                  success:function(data) {
                     result = data;
                  }
               });
               return result;
            }
            var _requestAbsence = requestAbsence();
            var _begin = _requestAbsence.EmployeeAbsence.begin ? _requestAbsence.EmployeeAbsence.begin : '';
            var _value = _requestAbsence.EmployeeAbsence.total ? _requestAbsence.EmployeeAbsence.total : 0;
            var _name = args.item.name;
            return {
                valid : _begin == '' || _value == 0,
                message : $this.t('You can not override because %s is already values.' , _name)
            };
        }
        var  data = <?php echo json_encode($dataView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
            employee_id : {defaulValue : '<?php echo $employee_id; ?>' , allowEmpty : false},
            year : {defaulValue : '<?php echo $start; ?>' , allowEmpty : false},
            total : {defaulValue : '',allowEmpty : false},
            begin : {defaulValue : '' , allowEmpty : false}
        };
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        $this.init($('#project_container'),data,columns , {
            enableAddRow : false
        });
    })(jQuery);
</script>
