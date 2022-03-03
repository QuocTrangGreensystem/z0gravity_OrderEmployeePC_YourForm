<?php //echo $html->script('jquery.dataTables');                                                                                                                                                                                                                             ?>
<?php //echo $html->css('jquery.ui.custom');                                                                                                                                                                                                                             ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('slick_grid/slick.grid_v2'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common_v2'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    .slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.select-employees{margin:3px;}.select-employees td{overflow:hidden;padding:2px 5px;}.select-employees .no-employee .input label{font-weight:700!important;}.select-employees label{display:inline!important;width:auto!important;}.context-menu-filter{clear:both;overflow:hidden;background-color:#004787;padding:3px;}.context-menu-filter input{width:100%!important;border:0!important;float:none!important;line-height:normal!important;background:0!important;margin:0!important;padding:0!important;}.context-menu-filter span{display:block;background:url(../css/images/search_label.gif) no-repeat 2px center;padding-left:17px;background-color:#fff;border:1px solid #D4D4D4;}.context-menu-shadow{background-color:#FFF!important;}.context-menu .notmatch,.wd-displays{display:none;}
</style>
<!-- export excel  -->

<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;    
        }
    </style>
<![endif]-->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_teams', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
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
                    <h2 class="wd-t1"><?php echo sprintf(__("Activity Teams List of %s", true), $activityName['Activity']['name']); ?></h2>				
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
                <div class="wd-table" id="project_container" style="width:100%;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden;">

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
<?php echo $html->script('responsive_table.js'); ?>
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
        'id' => 'project_function_id',
        'field' => 'project_function_id',
        'name' => __('Skill', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.selectBox',
        //'validator' => 'DateValidate.functions'
    ),
    array(
        'id' => 'profit_center_id',
        'field' => 'profit_center_id',
        'name' => __('Profit Center', true),
        'width' => 220,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.Profit',
        'formatter' => 'Slick.Formatters.Profit',
        'validator' => 'DateValidate.Profit'
    ),
    array(
        'id' => 'employee_id',
        'field' => 'employee_id',
        'name' => __('Employee', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.Employee',
        'formatter' => 'Slick.Formatters.Employee',
        'validator' => 'DateValidate.Employee'
    ),
    array(
        'id' => 'price_by_date',
        'field' => 'price_by_date',
        'name' => __('Price by date', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
        'validator' => 'DateValidate.Price'
    ),
    array(
        'id' => 'work_expected',
        'field' => 'work_expected',
        'name' => __('Work Expected', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox'
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
    'project_function_id' => $projectFunctions,
    'profit_center_id' => $profitCenters,
    'employee_id' => $employees,
);
foreach ($projectTeams as $projectTeam) {
    $data = array(
        'id' => $projectTeam['ProjectTeam']['id'],
        'project_id' => $projectName['Project']['id'],
        'no.' => $i++,
        'backup' => array(),
        'profit' => array(),
        'employee_id' => array()
    );
    $data['project_function_id'] = $projectTeam['ProjectTeam']['project_function_id'];
    //$data['profit_center_id'] = $projectTeam['ProjectTeam']['profit_center_id'];
    $data['price_by_date'] = $projectTeam['ProjectTeam']['price_by_date'];
    $data['work_expected'] = $projectTeam['ProjectTeam']['work_expected'];

    if ($projectTeam['ProjectFunctionEmployeeRefer']) {
        foreach ($projectTeam['ProjectFunctionEmployeeRefer'] as $refer) {
            if(!isset($data['profit_center_id'][$refer['profit_center_id']])){
                //do nothing
            }
            $data['profit_center_id'][$refer['profit_center_id']] = $refer['profit_center_id'];
            if(!empty($refer['employee_id'])){
                $data['employee_id'][] = $refer['employee_id'];
                $data['backup'][$refer['employee_id']] = !empty($refer['is_backup']) ? "1" : "0";
                $data['profit'][$refer['employee_id']] = $refer['profit_center_id'];
            }
        }
    } else {
        $data['employee_id'] = array();
        $data['profit_center_id'] = array();
    }
    $data['action.'] = '';

    $dataView[] = $data;
}

$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('controller' => 'project_teams', 'action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var DateValidate = {};
    
    (function($){
        
        $(function(){
        
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            $this.onApplyValue = function(item){
                $.extend(item, {backup : [] , profit : []})
            };
            // For validate date
            var projectName = <?php echo json_encode($projectName['Project']); ?>;
            var backup = {
                regex :  /<strong>\(B\)<\/strong>/,
                text : '<strong>(B)</strong>'
            };
            
            var actionTemplate =  $('#action-template').html();
            $.extend(Slick.Formatters,{
                Profit : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        if($this.selectMaps['profit_center_id'][val]){
                            _value.push($this.selectMaps['profit_center_id'][val]); 
                        }
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },
                Employee : function(row, cell, value, columnDef, dataContext){
                    var _value = [];
                    $.each(value, function(i,val){
                        _value.push($this.selectMaps['employee_id'][val] + (dataContext.backup[val] == '1' ? backup.text : '')); 
                    });
                    return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.project_id,$this.selectMaps['project_function_id'][dataContext.project_function_id]), columnDef, dataContext);
                }
            });
            var employeeBeforeEdit = new Array();
            var profiCenterBeforeEdit = new Array();
            $this.onBeforeEdit = function(args){
                if(args.item){
                    employeeBeforeEdit = args.item.employee_id ? args.item.employee_id : '';
                    profiCenterBeforeEdit = args.item.profit_center_id ? args.item.profit_center_id : '';
                }
                return true;
            }
            DateValidate.functions = function(value, args){              
                var result = true;
                $.each(args.grid.getData().getItems(), function(undefined,row){
                    if(value == row.project_function_id && row.profit_center_id == args.item.profit_center_id){
                        return result = false;
                    }
                });
                return {
                    valid : true,
                    message : $this.t('This function and profit center of project is exist.')
                };
            };
            
            DateValidate.Price = function(value){
                return {
                    valid : /^(([1-9][0-9]{0,4})|(0))(\.[0-9]{0,1})?$/.test(value) && parseInt(value ,10) > 0,
                    message : $this.t('The Budget must be a number and greater than 0. For example : 1, 1.2')
                };
            };
            var assignEmployees = <?php echo json_encode($assignEmployees);?>;
            var assignProfitCenters = <?php echo json_encode($assignProfitCenters);?>;
            DateValidate.Employee = function(value , args){
                var employeeAfterEdit = args.item.employee_id;
                var employeeDeleles = new Array();
                $.each(employeeBeforeEdit, function(key, val){
                    if($.inArray(val, employeeAfterEdit) != -1){
                        //do nothing
                    } else {
                        employeeDeleles.push(val);
                    }  
                });
                var isAssigns = new Array();
                if(employeeDeleles.length > 0){
                    $.each(employeeDeleles, function(key, val){
                        if($.inArray(val, assignEmployees) != -1){
                            //do nothing
                            isAssigns.push(val);
                        } else {
                            //employeeDeleles.push(val);
                        }  
                    });
                }
                var result = true;
                var _message = '';
                if(isAssigns.length > 0){
                    var textName = new Array();
                    $.each(isAssigns, function(ind, val){
                        textName.push($this.selectMaps.employee_id[val]);
                    });
                    textName = textName.join(', ');
                    _message = 'Employee: '+textName+' have assign in Project Task.';
                    result = false;
                }
                return {
                    valid : result,
                    message : $this.t(_message)
                };
            };
            
            DateValidate.Profit = function(value , args){
                var profitAfterEdit = args.item.profit_center_id;
                var profitDeleles = new Array();
                $.each(profiCenterBeforeEdit, function(key, val){
                    if($.inArray(val, profitAfterEdit) != -1){
                        //do nothing
                    } else {
                        profitDeleles.push(val);
                    }  
                });
                var isAssigns = new Array();
                if(profitDeleles.length > 0){
                    $.each(profitDeleles, function(key, val){
                        if($.inArray(val, assignProfitCenters) != -1){
                            //do nothing
                            isAssigns.push(val);
                        } else {
                            //employeeDeleles.push(val);
                        }  
                    });
                }
                var result = true;
                var _message = '';
                if(isAssigns.length > 0){
                    var textName = new Array();
                    $.each(isAssigns, function(ind, val){
                        textName.push($this.selectMaps.profit_center_id[val]);
                    });
                    textName = textName.join(', ');
                    _message = 'Profit Center: '+textName+' have assign in Project Task.';
                    result = false;
                }
                return {
                    valid : result,
                    message : $this.t(_message)
                };
            };
            
            var initMenuFilter = function($menu){
                var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                $menu.before($filter);
        
                var timeoutID = null, searchHandler = function(){
                    var val = $(this).val();
                    var te = $($menu).find('tbody tr td.wd-employ-data div label').html();
                    
                    $($menu).find('tbody tr td.wd-employ-data div label').each(function(){
                        var $label = $(this).html();
                        $label = $label.toLowerCase();
                        val = val.toLowerCase();
                        if(!val.length || $label.indexOf(val) != -1 || !val){
                            //$(this).parent().css('display', 'block');
                            //$(this).parent().next().css('display', 'block');
                            $(this).parent().parent().parent().removeClass('wd-displays');
                        } else{
                            //$(this).parent().parent().parent().css('display', 'none');
                            //$(this).parent().next().css('display', 'none');
                            $(this).parent().parent().parent().addClass('wd-displays');
                        }
                    });
                };
        
                $filter.find('input').click(function(e){
                    e.stopImmediatePropagation();
                }).keyup(function(){
                    var self = this;
                    clearTimeout(timeoutID);
                    timeoutID = setTimeout(function(){
                        searchHandler.call(self);
                    } , 200);
                });
        
            };
            //initMenuFilter($('.multiSelectOptions'));
            
            $.extend(Slick.Editors,{
                Employee : function(args){
                    var $options,hasChange = false,isCreated = false;
                    var defaultValue = [];
                    var scope = this;
                    var preload = function(){
                        // Get Ajax Employee
                        var getEmployee = function(profit,funcs){
                            if(scope.getValue(true) === true){
                                return;
                            }
                            scope.setValue(true);
                            $.ajax({
                                url : '<?php echo $html->url(array('controller' => 'project_teams', 'action' => 'get_employees', $projectName['Project']['id'])); ?>',
                                cache :  false,
                                type : 'GET',
                                data : {
                                    profit : profit,
                                    funcs : funcs,
                                    id : args.item.employee_id
                                },
                                success : function(data){
                                    $options.html(data);
                                    scope.input.html('');
                                    scope.setValue(defaultValue);
                                    scope.input.select();
                                    // filter menu
                                    initMenuFilter($('.select-employees'));
                                    
                                    $options.find('.id-hander').each(function(){
                                        var $el =  $(this);
                                        $.each(defaultValue, function(undefined,v){
                                            if($el.val() == v){
                                                $el.prop('checked' , true);
                                                if(args.item.backup[v] == '1'){
                                                    $el.closest('tr').find('.bk-hander').prop('checked' , true);
                                                }
                                                return false;
                                            }
                                        });
                                    }).add($options.find('.bk-hander')).click(function(){
                                        var list = [],c = args.column.id ,
                                        $hander = $(this).closest('tr').find('.id-hander');
                                        //addItem(args, args.item);
                                        args.item.backup = {};
                                        args.item.profit = {};
                                        if(this.checked && !$hander.is(':checked')){
                                            $hander.prop('checked' , true);
                                        }
                                        $options.find('.id-hander:checked').each(function(){
                                            var $el =  $(this),id= $el.val(),pt = $el.attr('profit');
                                            list.push(id);
                                            args.item.profit[id] = pt;
                                            args.item.backup[id] = $el.closest('tr').find('.bk-hander:checked').length > 0 ? 1 : 0;
                                        });
                                        args.item[c] = list;
                                        hasChange = true;
                                        scope.setValue(list);
                                    });
                                    $options.find('.no-employee input').click(function(){
                                        getEmployee(args.item['profit_center_id'],'');
                                    });
                                }
                            });
                        };
                        getEmployee(args.item['profit_center_id'] || 0,args.item['project_function_id'] || 0);
                    };
                    $.extend(this, new BaseSlickEditor(args));
                    
                    $options = $('<div class="multiSelectOptions" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px;"></div>').appendTo('body');
                    var hideOption = function(){
                        scope.input.removeClass('active').removeClass('hover');
                        $options.css({
                            visibility:'hidden',
                            display : 'none'
                        });
                    }
                    this.input = $('<a href="javascript:void(0);" class="multiSelect"></a>')
                    .appendTo(args.container)
                    .hover( function() {
                        scope.input.addClass('hover');
                    }, function() {
                        scope.input.removeClass('hover');
                    })
                    .click( function(e) {
                        // Show/hide on click
                        if(scope.input.hasClass('active')) {
                            hideOption();
                        } else {
                            var offset = scope.input.addClass('active').offset();
                            $options.css({
                                top:  offset.top + scope.input.outerHeight() + 'px',
                                left: offset.left + 'px',
                                visibility:'visible',
                                display : 'block'
                            });
                            if(scope.input.width() < 320){
                                $options.width(320);
                            }
                        }
                        if(e.stopPropagation) {
                            e.stopPropagation();
                        } else {
                            e.returnValue = false;
                        } 
                        return false;
                    });
                     
                    $(document).click( function(event) {
                        if(!($(event.target).parents().andSelf().is('.multiSelectOptions'))){
                            hideOption();
                        }
                    });
                    
                    var destroy = this.destroy;
                    this.destroy = function () {
                        $options.remove();
                        destroy.call(this, $.makeArray(arguments));
                    };

                    this.getValue = function (val) {
                        if(this.input.html() == 'Loading ...'){
                            if(val ==true){
                                return true;
                            }
                            return '';
                        }
                        return this.input.html().split(',');
                    };

                    this.setValue = function (val) {
                        if(val === true){
                            val = 'Loading ...';
                        }else{
                            val = Slick.Formatters.Employee(null,null,val, args.column ,args.item);
                        }
                        this.input.html(val);
                    };

                    this.loadValue = function (item) {
                        defaultValue = item[args.column.field] || "";
                    };

                    this.serializeValue = function () {
                        if(!isCreated){
                            this.loadValue(args.item);
                            preload();
                        }
                        return scope.getValue();
                    };
                    
                    var applyValue = this.applyValue;
                    this.applyValue = function (item, state) {
                        if($.isEmptyObject(item)){
                            applyValue.call(this, item , state);
                        }
                        $.extend(item ,args.item , true);
                    };

                    this.isValueChanged = function () {
                        return (hasChange == true);
                    };

                    this.validate = function () {
                        var option = $this.fields[args.column.id] || {};
                        var result = {
            				valid: true,
            				message: typeof option.message != 'undefined' ? option.message : $this.t('This information is not blank!')
            			},val = this.getValue();
            			if(option.allowEmpty === false && !val.length && !this.isCreate){
            				result.valid = false;
            			}
            			if(result.valid && val.length){
            				if(option.maxLength && val.length > option.maxLength){
            					result = {
            						valid: false,
            						message: $this.t('Please enter must be no larger than %s characters long.' , option.maxLength)
            					};
            				}else if($.isFunction(args.column.validator)){
            					result = args.column.validator.call(this, val, args);
            				}
            			}
            			if(!result.valid && result.message){
            				this.tooltip(result.message , result.callback);
            			}
            			return result;
                        //return {
//                            valid: true,
//                            msg: null
//                        };
                    };

                    this.focus();
                },
                Profit : function(args){
                    var $options,hasChange = false,isCreated = false;
                    var defaultValue = [];
                    var scope = this;
                    var preload = function(){
                        // Get Ajax Employee
                        var getEmployee = function(employ,funcs){
                            if(scope.getValue(true) === true){
                                return;
                            }
                            scope.setValue(true);
                            $.ajax({
                                url : '<?php echo $html->url(array('controller' => 'project_teams', 'action' => 'getProfitCenter', $projectName['Project']['id'])); ?>',
                                cache :  false,
                                async: false, 
                                type : 'GET',
                                data : {
                                    employ : employ,
                                    funcs : funcs,
                                    id : args.item.profit_center_id
                                },
                                success : function(data){
                                    $options.html(data);
                                    scope.input.html('');
                                    scope.setValue(defaultValue);
                                    scope.input.select();
                                    // filter menu
                                    initMenuFilter($('.select-employees'));
                                    
                                    $options.find('.id-hander').each(function(){
                                        var $el =  $(this);
                                        $.each(defaultValue, function(undefined,v){
                                            if($el.val() == v){
                                                $el.prop('checked' , true);
                                                if(args.item.backup[v] == '1'){
                                                    $el.closest('tr').find('.bk-hander').prop('checked' , true);
                                                }
                                                return false;
                                            }
                                        });
                                    }).add($options.find('.bk-hander')).click(function(){
                                        var list = [],c = args.column.id ,
                                        $hander = $(this).closest('tr').find('.id-hander');
                                        //addItem(args, args.item);
                                        args.item.backup = {};
                                        args.item.profit = {};
                                        if(this.checked && !$hander.is(':checked')){
                                            $hander.prop('checked' , true);
                                        }
                                        $options.find('.id-hander:checked').each(function(){
                                            var $el =  $(this),id= $el.val(),pt = $el.attr('profit');
                                            list.push(id);
                                            //args.item.profit[id] = pt;
                                            //args.item.backup[id] = $el.closest('tr').find('.bk-hander:checked').length > 0 ? 1 : 0;
                                        });
                                        args.item[c] = list;
                                        hasChange = true;
                                        scope.setValue(list);
                                    });
                                    $options.find('.no-employee input').click(function(){
                                        getEmployee(args.item['employee_id'],'');
                                    });
                                }
                            });
                        };
                        getEmployee(args.item['employee_id'] || 0,args.item['project_function_id'] || 0);
                    };
                    $.extend(this, new BaseSlickEditor(args));
                    
                    $options = $('<div class="multiSelectOptions" style="position: absolute; z-index: 99999; visibility: hidden;max-height:150px;"></div>').appendTo('body');
                    var hideOption = function(){
                        scope.input.removeClass('active').removeClass('hover');
                        $options.css({
                            visibility:'hidden',
                            display : 'none'
                        });
                    }
                    this.input = $('<a href="javascript:void(0);" class="multiSelect"></a>')
                    .appendTo(args.container)
                    .hover( function() {
                        scope.input.addClass('hover');
                    }, function() {
                        scope.input.removeClass('hover');
                    })
                    .click( function(e) {
                        // Show/hide on click
                        if(scope.input.hasClass('active')) {
                            hideOption();
                        } else {
                            var offset = scope.input.addClass('active').offset();
                            $options.css({
                                top:  offset.top + scope.input.outerHeight() + 'px',
                                left: offset.left + 'px',
                                visibility:'visible',
                                display : 'block'
                            });
                            if(scope.input.width() < 320){
                                $options.width(320);
                            }
                        }
                        if(e.stopPropagation) {
                            e.stopPropagation();
                        } else {
                            e.returnValue = false;
                        } 
                        return false;
                    });
                        
                    $(document).click( function(event) {
                        if(!($(event.target).parents().andSelf().is('.multiSelectOptions'))){
                            hideOption();
                        }
                    });
                    
                    var destroy = this.destroy;
                    this.destroy = function () {
                        $options.remove();
                        destroy.call(this, $.makeArray(arguments));
                    };

                    this.getValue = function (val) {
                        if(this.input.html() == 'Loading ...'){
                            if(val ==true){
                                return true;
                            }
                            return '';
                        }
                        return this.input.html().split(',');
                    };

                    this.setValue = function (val) {
                        if(val === true){
                            val = 'Loading ...';
                        }else{
                            val = Slick.Formatters.Profit(null,null,val, args.column ,args.item);
                        }
                        this.input.html(val);
                    };

                    this.loadValue = function (item) {
                        defaultValue = item[args.column.field] || "";
                    };

                    this.serializeValue = function () {
                        if(!isCreated){
                            this.loadValue(args.item);
                            preload();
                        }
                        return scope.getValue();
                    };
                    
                    var applyValue = this.applyValue;
                    this.applyValue = function (item, state) {
                        if($.isEmptyObject(item)){
                            applyValue.call(this, item , state);
                        }
                        $.extend(item ,args.item , true);
                    };

                    this.isValueChanged = function () {
                        return (hasChange == true);
                    };

                    this.validate = function () {
                        var option = $this.fields[args.column.id] || {};
                        var result = {
            				valid: true,
            				message: typeof option.message != 'undefined' ? option.message : $this.t('This information is not blank!')
            			},val = this.getValue();
            			if(option.allowEmpty === false && !val.length && !this.isCreate){
            				result.valid = false;
            			}
            			if(result.valid && val.length){
            				if(option.maxLength && val.length > option.maxLength){
            					result = {
            						valid: false,
            						message: $this.t('Please enter must be no larger than %s characters long.' , option.maxLength)
            					};
            				}else if($.isFunction(args.column.validator)){
            					result = args.column.validator.call(this, val, args);
            				}
            			}
            			if(!result.valid && result.message){
            				this.tooltip(result.message , result.callback);
            			}
            			return result;
                        //return {
//                            valid: true,
//                            msg: null
//                        };
                    };

                    this.focus();
                }
            });
            
            var  data = <?php echo json_encode($dataView); ?>;
            var  employeeRefers = <?php echo json_encode($employeeRefers); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                project_id : {defaulValue : projectName['id'], allowEmpty : false},
                project_function_id : {defaulValue : ''},
                profit_center_id : {defaulValue : ''},
                price_by_date : {defaulValue : 0},
                work_expected : {defaulValue : ''},
                employee_id : {defaulValue : ''},
                backup: {defaulValue : ''}
            };
            $this.url =  '<?php echo $html->url(array('controller' => 'project_teams', 'action' => 'update')); ?>';
            $this.init($('#project_container'),data,columns);
            $this.onCellChange = function(args){
                if(args.item){
                    var groupPcs = new Array();
                    if(args.item.employee_id && args.item.employee_id.length != 0){
                        $.each(args.item.employee_id, function(ind, val){
                            var _profitCenter = employeeRefers[val] ? employeeRefers[val] : 0;
                            if(groupPcs[_profitCenter] || _profitCenter == 0){
                                //do nothing
                            }
                            groupPcs[_profitCenter] = _profitCenter;
                        });
                        var _group = {};
                        if(args.item.profit_center_id){
                            var currentPC = $.map(args.item.profit_center_id, function(value, index) {
                                return [value];
                            });
                            _group = groupPcs.concat(currentPC);
                        } else {
                            _group = groupPcs;
                        }
                        _group = _group ? $.unique(_group) : {};
                        args.item.profit_center_id = _group;
                    }
                    //var currentRow = args.grid.getData().getRowById(args.item.id);
                    //args.grid.getData().getItems()[currentRow].price_by_date = 100;
                }
                return true;
            }
        
        });
        
    })(jQuery);
</script>