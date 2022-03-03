<style type="text/css">
#wd-container-footer{
    display: none;
}
body{
    overflow: hidden;
}
</style>
<?php
echo $this->Html->css(array(
    'slick_grid/slick.grid.activity',
    'jquery.multiSelect',
    'projects',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit'
));
echo $this->Html->script(array(
    'history_filter',
    'jquery.multiSelect',
    //'responsive_table',
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
$menu = $this->requestAction('/menus/getMenu/project_budget_internals/index');
$langCode = Configure::read('Config.langCode');
if( $langCode == 'fr' )$langCode .= 'e';
else if( $langCode == 'en' )$langCode .= 'g';
$canModified = (($modifyBudget == true && !$_isProfile) || ($_isProfile && $_canWrite)) ? true : false;
?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .slick-viewport-right{
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    .slick-viewport-left{
        overflow: hidden !important;
    }
	.wd-tab{
		max-width: 1920px;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <a href="javascript:void(0);" class="btn btn-plus-green" id="new-internal" onclick="addInternalCost();" title="<?php __('Add an internal cost') ?>"><span><?php __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost') ?></span></a>
                    <!-- <a id="export-submitplus" href="javascript:void(0);" class="export-excel-icon-all" style="margin-right:5px; " title="<?php __('Export Excel')?>"><span><?php __('Export Excel+ ') ?></span></a> -->
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
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
              </div></div>

        </div>
    </div>
</div>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_budget_internals', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>

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
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$columns = array(
    // array(
    //     'id' => 'no.',
    //     'field' => 'no.',
    //     'name' => '#',
    //     'width' => 40,
    //     'sortable' => true,
    //     'resizable' => false,
    //     'noFilter' => 1,
    // ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Name', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBoxCustom'
    ),
    array(
        'id' => 'validation_date',
        'field' => 'validation_date',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Validation date', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker',
        'datatype' => 'datetime'
    ),
    array(
        'id' => 'budget_erro',
        'field' => 'budget_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'forecast_erro',
        'field' => 'forecast_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'var_percent',
        'field' => 'var_percent',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Var % (€)', true),
        'width' => 60,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'engaged_erro',
        'field' => 'engaged_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'remain_erro',
        'field' => 'remain_erro',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Remain €', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'budget_md',
        'field' => 'budget_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Budget M.D', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.manDayValue'
    ),
    array(
        'id' => 'forecast_md',
        'field' => 'forecast_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Forecast M.D', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'var_percent_md',
        'field' => 'var_percent_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Var % (' . ' M.D)', true),
        'width' => 60,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'engaged_md',
        'field' => 'engaged_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Engaged M.D', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'remain_md',
        'field' => 'remain_md',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Remain M.D', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'average',
        'field' => 'average',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Average daily rate €', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
    ),
     array(
        'id' => 'profit_center_id',
        'field' => 'profit_center_id',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Profit Center', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
     array(
        'id' => 'funder_id',
        'field' => 'funder_id',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Funder', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    // array(
    //     'id' => 'action.',
    //     'field' => 'action.',
    //     'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Action', true),
    //     'width' => 70,
    //     'sortable' => false,
    //     'resizable' => true,
    //     'noFilter' => 1,
    //     'formatter' => 'Slick.Formatters.Action'
    // )
);
$settings = $this->requestAction('/translations/getSettings',
    array('pass' => array(
        'Internal_Cost',
        array('internal_cost')
    )
));
// //su dung setting de sort lai $columns
$columns = Set::combine($columns, '{n}.field', '{n}');
$new = array();
foreach ($settings as $field => $value) {
    if( isset($columns[$field]) )
        $new[] = $columns[$field];
}
$columns = $new;
array_push($columns, array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    ));
array_unshift($columns, array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ));


$i = 1;
$dataView = array();
$selectMaps = array(
    'profit_center_id' => $profits,
    'funder_id' => $funders
);
$count = 0;
$totalAverages = 0;
foreach ($budgets as $budget) {
    $data = array(
        'id' => $budget['ProjectBudgetInternalDetail']['id'],
        'project_id' => $budget['ProjectBudgetInternalDetail']['project_id'],
        'activity_id' => $activityLinked,
        'no.' => $i++
    );
    $data['name'] = (string) $budget['ProjectBudgetInternalDetail']['name'];
    $data['validation_date'] = $str_utility->convertToVNDate($budget['ProjectBudgetInternalDetail']['validation_date']);
    $data['budget_md'] = $budget['ProjectBudgetInternalDetail']['budget_md'];
    $budget_md = !empty($budget['ProjectBudgetInternalDetail']['budget_md']) ? $budget['ProjectBudgetInternalDetail']['budget_md'] : 0;
    $averages = !empty($budget['ProjectBudgetInternalDetail']['average']) ? $budget['ProjectBudgetInternalDetail']['average'] : 0;
    $data['average'] = $averages;
    $totalAverages += $averages;
    $data['budget_erro'] = $averages*$budget_md;
    $data['profit_center_id'] = $budget['ProjectBudgetInternalDetail']['profit_center_id'];
    $data['funder_id'] = $budget['ProjectBudgetInternalDetail']['funder_id'];
    $data['action.'] = '';
    $count++;
    $dataView[] = $data;
}
if($count == 0){
    $totalAverages = 0;
} else {
    $totalAverages = round($totalAverages/$count, 2);
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true)
);
$viewManDay = __($md, true);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
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
    var DateValidate = {};
    (function($){

        $(function(){
            var $this = SlickGridCustom, gridControl;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }

            $.extend(Slick.Editors,{
                forecastValue : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    this.input.attr('maxlength' , 11).keypress(function(e){
                        var key = e.keyCode ? e.keyCode : e.which;
                        if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
                            return;
                        }
                        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        if(!/^([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                },
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
                        if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                            e.preventDefault();
                            return false;
                        }
                    });
                },
                textBoxCustom : function(args){
                    $.extend(this, new BaseSlickEditor(args));
                    this.input = $("<input type='text' placeholder='New Internal Cost'/>")
                    .appendTo(args.container).attr('rel','no-history').addClass('editor-text placeholder');
                    this.focus();
                },
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
                    message : $this.t('<?php echo __('The Budget must be a number and greater than 0', true)?>')
                };
            }

            var actionTemplate =  $('#action-template').html();
            //kiem tra lai dat vi tri bien dung khong?
            var budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,dataContext.name), columnDef, dataContext);
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + budget_settings + '</span>', columnDef, dataContext);
                },
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ' + viewManDay, columnDef, dataContext);
                }
            });

            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: true,
                frozenColumn: 1
            };
            var activityLinked = <?php echo json_encode($activityLinked); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false},
                validation_date : {defaulValue : ''},
                budget_md : {defaulValue : ''},
                average : {defaulValue : ''},
                profit_center_id : {defaulValue : 0},
                funder_id : {defaulValue : 0},
                activity_id: {defaulValue : activityLinked}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update_detail')); ?>';
            gridControl = $this.init($('#project_container'),data,columns, options);
            $this.onBeforeEdit = function(args){
                //if(args.column.field == 'average_daily_rate'){
//                    return false;
//                }
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').closest('div').parent().addClass('rowCurrentEdit');
                //$('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                return true;
            }

            var getDataProjectTasks = <?php echo json_encode($getDataProjectTasks); ?>;
            var  _totalAverages = <?php echo json_encode($totalAverages); ?>;
            var  externalBudgets = <?php echo json_encode($externalBudgets); ?>;
            var  externalConsumeds = <?php echo json_encode($externalConsumeds); ?>;
            var remain_md = getDataProjectTasks.remain ? (parseFloat(getDataProjectTasks.remain) - (parseFloat(externalBudgets) - parseFloat(externalConsumeds))).toFixed(2) : 0;
            var consumed_md = getDataProjectTasks.consumed ? (getDataProjectTasks.consumed).toFixed(2) : 0;
            var forecast_md = (parseFloat(remain_md) + parseFloat(consumed_md)).toFixed(2);

            var engaged_erro = <?php echo json_encode($engagedErro); ?>;
            engaged_erro = engaged_erro.toFixed(2);
            var _engaged_erro = engaged_erro;

            var remain_erro = (remain_md*_totalAverages).toFixed(2);
            var forecast_erro = (parseFloat(engaged_erro)+parseFloat(remain_erro)).toFixed(2);
            var _forecast_erro = forecast_erro;

            var _budgetMds = _budgetErros = 0;
            $.each(data, function(ind, vl){
                _budgetMds += vl.budget_md ? parseFloat(vl.budget_md) : 0;
                _budgetErros += parseFloat(vl.budget_erro);
            });
            var _varMds = _varErros = 0;
            if(_budgetMds != 0){
                _varMds = ((forecast_md/_budgetMds - 1)*100).toFixed(2);
            }
            if(_budgetErros != 0){
                _varErros = ((forecast_erro/_budgetErros - 1)*100).toFixed(2);
            }
            _budgetMds = number_format(_budgetMds, 2, ',', ' ') + ' ' + viewManDay;
            _budgetErros = number_format(_budgetErros, 2, ',', ' ') +' ' + budget_settings;
            forecast_erro = number_format(forecast_erro, 2, ',', ' ') +' ' + budget_settings;
            engaged_erro = number_format(engaged_erro, 2, ',', ' ')+' ' + budget_settings;
            remain_erro = number_format(remain_erro, 2, ',', ' ')+' ' + budget_settings;
            _varErros = number_format(_varErros, 2, ',', ' ') + ' %';
            _varMds = number_format(_varMds, 2, ',', ' ') + ' %';
            _totalAverages = number_format(_totalAverages, 2, ',', ' ') +' ' + budget_settings;
            gridControl.onSort.subscribe(function (e, args) {
                $('.row-number').parent().addClass('row-number-custom');
            });
            gridControl.onScroll.subscribe(function (e, args) {
                $('.row-number').parent().addClass('row-number-custom');
            });
            $this.onCellChange = function(args){
                $('.row-number').parent().addClass('row-number-custom');
                if(args.item){
                    var _isBudgetMd = args.item.budget_md ? parseFloat(args.item.budget_md) : 0;
                    var _averages = args.item.average ? parseFloat(args.item.average) : 0;
                    args.item.budget_erro = (_isBudgetMd*_averages);
                    _budgetMds = _budgetErros = 0;
                    var count = 0;
                    _totalAverages = 0;
                    $.each(data, function(ind, vl){
                        _budgetMds += vl.budget_md ? parseFloat(vl.budget_md) : 0;
                        _budgetErros += parseFloat(vl.budget_erro);
                        _totalAverages += vl.average ? parseFloat(vl.average) : 0;
                        count++;
                    });
                    _totalAverages = (_totalAverages/count).toFixed(2);
                    _varMds = _varErros = 0;
                    if(_budgetMds != 0){
                        _varMds = ((forecast_md/_budgetMds - 1)*100).toFixed(2);
                    }
                    if(_budgetErros != 0){
                        _varErros = ((_forecast_erro/_budgetErros - 1)*100).toFixed(2);
                    }
                    _budgetMds = number_format(_budgetMds, 2, ',', ' ') + ' ' + viewManDay;
                    _budgetErros = number_format(_budgetErros, 2, ',', ' ') +' ' + budget_settings;
                    _varErros = number_format(_varErros, 2, ',', ' ') + ' %';
                    _varMds = number_format(_varMds, 2, ',', ' ') + ' %';
                    remain_erro = (remain_md*_totalAverages).toFixed(2);
                    forecast_erro = (parseFloat(_engaged_erro)+parseFloat(remain_erro)).toFixed(2);
                    _totalAverages = number_format(_totalAverages, 2, ',', ' ') +' ' + budget_settings;
                    forecast_erro = number_format(forecast_erro, 2, ',', ' ') +' ' + budget_settings;
                    remain_erro = number_format(remain_erro, 2, ',', ' ') +' ' + budget_settings;
                    $('#gs-budget-md').html(_budgetMds);
                    $('#gs-budget-erro').html(_budgetErros);
                    $('#gs-var-md').html(_varMds);
                    $('#gs-var-erro').html(_varErros);
                    $('#gs-average-daily-rate').html(_totalAverages);
                    $('#gs-remain-erro').html(remain_erro);
                    $('#gs-forecast-erro').html(forecast_erro);
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
            addInternalCost = function(){
                gridControl.gotoCell(data.length, 1, true);
            }
             // Chuyen header sang mau xanh la cay
            var headers = $('.slick-header-columns').get(1).children;
            $.each(headers, function(index, val){
                var id = headers[index].id;
                if( id.indexOf('md') != -1 ){
                    $('#'+id).addClass('gs-custom-cell-md-header');
                }
            });
            var cols = gridControl.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            gridControl.onColumnsResized.subscribe(function (e, args) {
                var _cols = gridControl.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('.row-number').parent().addClass('row-number-custom');
            });
            $('.row-number').parent().addClass('row-number-custom');
            var _header = {
                name: {id: 'gs-name', val: 'Total', cl: 'gsEuro'},
                budget_erro: {id: 'gs-budget-erro',val: _budgetErros, cl: 'gsEuro'},
                forecast_erro: {id: 'gs-forecast-erro',val: forecast_erro, cl: 'gsEuro'},
                var_percent: {id: 'gs-var-erro',val: _varErros, cl: 'gsEuro'},
                engaged_erro: {id: 'gs-engaged-erro',val: engaged_erro, cl: 'gsEuro'},
                remain_erro: {id: 'gs-remain-erro',val: remain_erro, cl: 'gsEuro'},
                budget_md: {id: 'gs-budget-md',val: _budgetMds, cl: 'gsMd'},
                forecast_md: {id: 'gs-forecast-md',val: number_format(forecast_md, 2, ',', ' ') + ' ' + viewManDay, cl: 'gsMd'},
                var_percent_md: {id: 'gs-var-md',val: _varMds, cl: 'gsMd'},
                engaged_md: {id: 'gs-consumed-md',val: number_format(consumed_md, 2, ',', ' ')+ ' ' + viewManDay, cl: 'gsMd'},
                remain_md: {id: 'gs-remain-md',val: number_format(remain_md, 2, ',', ' ')+ ' ' + viewManDay, cl: 'gsMd'},
                average: {id: 'gs-average-daily-rate',val: _totalAverages, cl: 'gsEuro'},
            };
            var settings = <?php echo json_encode(array_keys($settings)); ?>;
            $.each(settings, function(index, field){
                if(_header[field]){
                    $(gridControl.getHeaderRowColumn(field)).html('<p id="' +_header[field].id+ '" class="' +_header[field].cl+ '">' +_header[field].val+ '</p>');
                }
            });
            $('.gsMd').parent().addClass('gs-custom-cell-md');
            $('#export-submitplus').click(function(){
                        var length = gridControl.getDataLength(),
                            list = [];
                        for(var i = 0; i<length; i++){
                            var item = gridControl.getDataItem(i);
                            list.push(item.id);
                        }
                        $('#export-item-list')
                        .val(list.join(','))
                        .closest('form')
                        .submit();

            });
        });
    })(jQuery);
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
<script>
    //format float number
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
</script>

<style type="text/css">
#wd-header-custom{height:30px;border:1px solid #E0E0E0;border-bottom:none;border-right:none!important;}
.slick-headerrow-columns div{margin-right:-7px;}.slick-headerrow-columns div p{padding-top:5px;font-weight:700;text-align: right;}#project_container{overflow:visible!important;}
.slick-headerrow-columns div{background-color:#b5d2e2;}
.gs-custom-cell-md-header{background:#75923C!important;}.gs-custom-cell-md{background-color:#C2D69A!important;}.cl-average-daily-rate{margin-left:-12px;width:135px;padding:7px;}.color-rate-loading{color:#ccc;}.color-rate-success{color:#3BBD43;}.color-rate-error{color:#F71230;}.slick-row.odd{background:#FFF!important;}.row-parent-custom{background-color:#EAF1FA;}.row-disabled-custom{background-color:#FAFAFA!important;}.row-number-custom{text-align:right;}.l3,.l4,.l5,.l6,.l7,.l9,.l10,.l11,.l12,.l14{background:#F5F5F5;}.row-current-edit{border-top:1px solid #004482!important;border-bottom:1px solid #004482!important;box-shadow:0 0 3px #004482;}
</style>
