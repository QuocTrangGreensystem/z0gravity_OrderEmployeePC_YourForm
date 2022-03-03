<?php //echo $html->script('jquery.dataTables');                                                                                                                                                                                           ?>
<?php //echo $html->css('jquery.ui.custom');                                                                                                                                                                                           ?>

<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php //echo $html->css('slick_grid/smoothness/jquery-ui-1.8.16.custom'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables'); ?>
<?php
    // ob_clean();
    // debug(1);
    // exit;    
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
</style>
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
   // echo $this->Form->create('Export', array(
//        'type' => 'POST',
//        'url' => array('controller' => 'project_evolutions', 'action' => 'export', $projectName['Project']['id'])));
//    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
//    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __d(sprintf($_domain, 'Internal_Cost'), "Internal Cost", true) . ': ' . $activityName['Activity']['name']; ?></h2>
                    <a href="javascript:void(0);" class="wd-add-project" id="new-internal" style="margin-right:5px;" onclick="addInternalCost();"><span><?php __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter" id="clean-filters" style="margin-right:5px;" title="Clean filters"></a>
                    <?php /*
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                    <a href="javascript:void(0);" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                    */ ?>
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
                <div id="pager" style="width:100%;height:36px; overflow: hidden; margin-top: 30px;">

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
        'editor' => 'Slick.Editors.datePicker'
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
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Var %', true),
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
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Var %', true),
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
        'name' => __d(sprintf($_domain, 'Profit_Center'), 'Profit Center', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
		'editor' => 'Slick.Editors.selectBox'
    ),
	 array(
        'id' => 'funder_id',
        'field' => 'funder_id',
        'name' => __d(sprintf($_domain, 'Funder'), 'Funder', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
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
        'activity_id' => $budget['ProjectBudgetInternalDetail']['activity_id'],
        'project_id' => $projectLinked,
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
	$data['profit_center_id'] = (string) $budget['ProjectBudgetInternalDetail']['profit_center_id'];
	$data['funder_id'] = (string) $budget['ProjectBudgetInternalDetail']['funder_id'];
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
$viewManDay = __('M.D', true);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
	var budgetCurrency = <?php echo json_encode($bg_currency); ?>;
    var DateValidate = {};
    (function($){
        
        $(function(){
            var $this = SlickGridCustom, gridControl;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var activityName = <?php echo json_encode($activityName['Activity']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }
            
            $.extend(Slick.Editors,{
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
                },
                numericValueBudget : function(args){
        			$.extend(this, new Slick.Editors.textBox(args));
        			this.input.attr('maxlength' , 10).keypress(function(e){
        				var key = e.keyCode ? e.keyCode : e.which;
        				if(!key || key == 8 || key == 13){
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
        
        
            //DateValidate.startDate = function(value){
//                value = getTime(value);
//                if(projectName['start_date'] == ''){
//                    _valid = true;
//                    _message = '';
//                    //_message = $this.t('Start-Date or End-Date of Project are missing. Please input these data before full-field this date-time field.');
//                } else {
//                    //_valid = value >= getTime(projectName['start_date']) && value <= getTime(projectName['end_date']);
//                    //_message = $this.t('Date closing must between %1$s and %2$s' ,projectName['start_date'], projectName['end_date']);
//                    _valid = value >= getTime(projectName['start_date']);
//                    _message = $this.t('Date closing must larger %1$s' ,projectName['start_date']);
//                }
//                return {
//                    valid : _valid,
//                    message : _message
//                };
//            }
            DateValidate.budgetBox = function(value){
                return {
                    valid : /^([1-9]|[1-9][0-9]*)$/.test(value) && parseInt(value ,10) > 0,
                    message : $this.t('The Budget must be a number and greater than 0')
                };
            }
        
            var actionTemplate =  $('#action-template').html();
            $.extend(Slick.Formatters,{
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.activity_id,dataContext.name), columnDef, dataContext);
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
        			return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> '+ budgetCurrency, columnDef, dataContext);
        		},
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
        			return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ' + viewManDay, columnDef, dataContext);
        		}
            });
        
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: false      
            };
            var projectLinked = <?php echo json_encode($projectLinked); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                activity_id : {defaulValue : activityName['id'], allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false},
                validation_date : {defaulValue : ''},
                budget_md : {defaulValue : ''},
                average : {defaulValue : ''},
				profit_center_id : {defaulValue : 0},
				funder_id : {defaulValue : 0},
                project_id: {defaulValue : projectLinked}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update_detail')); ?>';
            gridControl = $this.init($('#project_container'),data,columns, options);
            $this.onBeforeEdit = function(args){
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').parent().addClass('rowCurrentEdit');
                $('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                return true;
            }
            
            var getDataProjectTasks = <?php echo json_encode($getDataProjectTasks); ?>;
            
            var  _totalAverages = <?php echo json_encode($totalAverages); ?>;
            
            var remain_md = getDataProjectTasks.remain ? (getDataProjectTasks.remain).toFixed(2) : 0;
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
            _budgetErros = number_format(_budgetErros, 2, ',', ' ') + ' '+$bg_currency;
            forecast_erro = number_format(forecast_erro, 2, ',', ' ');
            engaged_erro = number_format(engaged_erro, 2, ',', ' ');
            remain_erro = number_format(remain_erro, 2, ',', ' ');
            _varErros = number_format(_varErros, 2, ',', ' ') + ' %';
            _varMds = number_format(_varMds, 2, ',', ' ') + ' %';
            _totalAverages = number_format(_totalAverages, 2, ',', ' ') + ' '+$bg_currency;
			gridControl.onSort.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
			});
			gridControl.onScroll.subscribe(function(args, e, scope){
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
                    _budgetErros = number_format(_budgetErros, 2, ',', ' ') + ' '+$bg_currency;
                    _varErros = number_format(_varErros, 2, ',', ' ') + ' %';
                    _varMds = number_format(_varMds, 2, ',', ' ') + ' %';
                    remain_erro = (remain_md*_totalAverages).toFixed(2);
                    forecast_erro = (parseFloat(_engaged_erro)+parseFloat(remain_erro)).toFixed(2);
                    _totalAverages = number_format(_totalAverages, 2, ',', ' ') + ' '+$bg_currency;
                    forecast_erro = number_format(forecast_erro, 2, ',', ' ') + ' '+$bg_currency;
                    remain_erro = number_format(remain_erro, 2, ',', ' ') + ' '+$bg_currency;
                    
                    $('#gs-budget-md p').html(_budgetMds);
                    $('#gs-budget-erro p').html(_budgetErros);
                    $('#gs-var-md p').html(_varMds);
                    $('#gs-var-erro p').html(_varErros);
                    $('#gs-average-daily-rate p').html(_totalAverages);
                    $('#gs-remain-erro p').html(remain_erro);
                    $('#gs-forecast-erro p').html(forecast_erro);
                }
                return true;
            }
            addInternalCost = function(){
                gridControl.gotoCell(data.length, 1, true);
            }
             // Chuyen header sang mau xanh la cay
            var headers = $('.slick-header-columns').get(0).children;
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
            var general = '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>';
            var hd = {
                name : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" style="text-align: left;"><p>Total</p></div>',
                // validation_date : '',
                budget_erro : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-budget-erro"><p>' +_budgetErros+ '</p></div>',
                forecast_erro : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-forecast-erro"><p>' +forecast_erro+ ' €</p></div>',
                var_percent : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-var-erro"><p>' +_varErros+ '</p></div>',
                engaged_erro : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro"><p>' +engaged_erro+ ' €</p></div>',
                remain_erro : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id ="gs-remain-erro"><p>' +remain_erro+ ' €</p></div>',
                budget_md : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-md" id="gs-budget-md"><p>' +_budgetMds+ '</p></div>',
                forecast_md : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-md"><p>' +number_format(forecast_md, 2, ',', ' ')+ ' '+viewManDay+'</p></div>',
                var_percent_md : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-md" id="gs-var-md"><p>' +_varMds+ '</p></div>',
                engaged_md : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-md"><p>' +number_format(consumed_md, 2, ',', ' ')+ ' '+viewManDay+'</p></div>',
                remain_md : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-md"><p>' +number_format(remain_md, 2, ',', ' ')+ ' '+viewManDay+'</p></div>',
                average : '<div class="ui-state-default slick-headerrow-column %s wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-average-daily-rate"><p>' +_totalAverages+ '</p></div>',
                // profit_center_id : '',
                // funder_id : ''
            };
            var settings = <?php echo json_encode(array_keys($settings)); ?>;
            var header = '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">'
                    + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>';
            var i = 1;
            $.each(settings, function(index, field){
                var classes = 'l' + i + ' r' + i + ' ' + field;
                if( hd[field] ){
                    header += hd[field].replace('%s', classes);
                } else {
                    header += general.replace('%s', classes);
                }
                i++;
            });
            header += '<div class="ui-state-default slick-headerrow-column l'+ i +' r'+i+' wd-row-custom wd-custom-cell gs-custom-cell-erro"></div></div>';
            $('.slick-header-columns').after(header);
             function setupScroll(){
                $("#scrollTopAbsenceContent").width($(".grid-canvas").width()+50);
                $("#scrollTopAbsence").width($(".slick-viewport").width());
            }
            setTimeout(function(){
                setupScroll();
            }, 2500);
            $("#scrollTopAbsence").scroll(function () {
                $(".slick-viewport").scrollLeft($("#scrollTopAbsence").scrollLeft());
            });
            $(".slick-viewport").scroll(function () {
                $("#scrollTopAbsence").scrollLeft($(".slick-viewport").scrollLeft());
            });
        });
        
    })(jQuery);
</script>
<script>
    var path = <?php echo json_encode($this->params['url']['url']); ?>;
    //format float number
    function number_format(number, decimals, dec_point, thousands_sep) {
      // http://kevin.vanzonneveld.net
      // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +     bugfix by: Michael White (http://getsprink.com)
      // +     bugfix by: Benjamin Lupton
      // +     bugfix by: Allan Jensen (http://www.winternet.no)
      // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
      // +     bugfix by: Howard Yeend
      // +    revised by: Luke Smith (http://lucassmith.name)
      // +     bugfix by: Diogo Resende
      // +     bugfix by: Rival
      // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
      // +   improved by: davook
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +      input by: Jay Klehr
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +      input by: Amir Habibi (http://www.residence-mixte.com/)
      // +     bugfix by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Theriault
      // +      input by: Amirouche
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // *     example 1: number_format(1234.56);
      // *     returns 1: '1,235'
      // *     example 2: number_format(1234.56, 2, ',', ' ');
      // *     returns 2: '1 234,56'
      // *     example 3: number_format(1234.5678, 2, '.', '');
      // *     returns 3: '1234.57'
      // *     example 4: number_format(67, 2, ',', '.');
      // *     returns 4: '67,00'
      // *     example 5: number_format(1000);
      // *     returns 5: '1,000'
      // *     example 6: number_format(67.311, 2);
      // *     returns 6: '67.31'
      // *     example 7: number_format(1000.55, 1);
      // *     returns 7: '1,000.6'
      // *     example 8: number_format(67000, 5, ',', '.');
      // *     returns 8: '67.000,00000'
      // *     example 9: number_format(0.9, 0);
      // *     returns 9: '1'
      // *    example 10: number_format('1.20', 2);
      // *    returns 10: '1.20'
      // *    example 11: number_format('1.20', 4);
      // *    returns 11: '1.2000'
      // *    example 12: number_format('1.2000', 3);
      // *    returns 12: '1.200'
      // *    example 13: number_format('1 000,50', 2, '.', ' ');
      // *    returns 13: '100 050.00'
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
    $(document).ready(function(){
        $('#clean-filters').click(function(){
            $.ajax({
                url: '/activity_budget_internals/clean_filters/',
                type: 'POST',
                data: {
                    path: path,
                },
                dataType: 'json',
                success: function(data){
                    location.reload();
                }
            });
            
        });
    });
</script>

<style type="text/css">
    #wd-header-custom{
        height: 30px; 
        border: 1px solid #E0E0E0; 
        border-bottom: none;
        border-right: none !important;
    }
    .wd-row-custom{
        margin-right: -7px;
    }
    .wd-row-custom p{
        padding-top: 5px;
        font-weight: bold;
    }
    .wd-custom-cell{
        background: none !important;
        border-right: none !important;
        /*width: 100%;*/
    }
    .slick-viewport{
        /*height: 76% !important;*/
    }
    #project_container{
        overflow: visible !important;
    }
    .gs-custom-cell-erro{
        background-color: #95B3D7 !important;
    }
    .gs-custom-cell-md-header{
        background: #75923C !important;
    }
    .gs-custom-cell-md{
        background-color: #C2D69A !important;
    }
    .cl-average-daily-rate{
        padding: 7px;
        margin-left: -12px;
        width: 135px;
    }
    .color-rate-loading{
        color: #ccc;
    }
    .color-rate-success{
        color: #3BBD43;
    }
    .color-rate-error{
        color: #F71230;
    }
    .slick-row.odd{
        background: #FFF !important;
    }
    .row-parent-custom{
        background-color: #EAF1FA;
    }
    .row-disabled-custom{
        background-color: #FAFAFA !important;
    }
    .row-number-custom{
        text-align: right;
    }
    .l3, .l4, .l5, .l6, .l7, .l9, .l10, .l11, .l12, .l14{
        background: #F5F5F5;
    }
    .row-current-edit{
        border-top: 1px solid #004482 !important;
        border-bottom: 1px solid #004482 !important;
        box-shadow: 0px 0px 3px #004482;
        /*
        zoom: 1;
        filter: progid:DXImageTransform.Microsoft.DropShadow(OffX=0, OffY=0, Color=#00FF3D),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=0),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=90),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=180),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=270),
                progid:DXImageTransform.Microsoft.Chroma(Color='#ffffff');
        
        filter: 
            progid:DXImageTransform.Microsoft.DropShadow(OffX=0, OffY=0, Color=#00FF3D),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=0),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=90),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=180),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=270);
        
        
        -ms-filter: 
        "progid:DXImageTransform.Microsoft.Shadow(Strength=15, Direction=0, Color='#00FF3D')",
        "progid:DXImageTransform.Microsoft.Shadow(Strength=15, Direction=180, Color='#00FF3D')"
        ;
        */
    }
</style>