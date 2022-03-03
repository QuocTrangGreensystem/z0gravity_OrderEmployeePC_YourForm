<?php
    echo $this->Html->css(array(
        'projects',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'slick_grid/slick.edit',
        //'slick_grid/slick.grid',
        'slick_grid/slick.grid.activity'
    ));
    echo $this->Html->script(array(
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid_custom',
        //'slick_grid/lib/jquery.event.drag-2.0.min',
        //'slick_grid/slick.grid',
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity'
    ));
    $md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
    $viewManDay = __($md, true);
    $viewEuro = __($budget_settings, true);
    $menu = $this->requestAction('/menus/getMenu/project_budget_provisionals/index');
    $canModified = (($modifyBudget == true && !$_isProfile) || ($_isProfile && $_canWrite)) ? true : false;
    $update_budget = $employee_info['Employee']['update_budget'];
    $roleName = $employee_info['Role']['name'];
?>
<style>
.row-number-custom {
    text-align: right;
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
.wd-tab{
	max-width: 1920px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo $menu['Menu']['name_' . $longCode] . ': ' . $projects['Project']['project_name']; ?></h2>
                    <select style="margin-right:3px; padding: 6px;" class="wd-customs" id="viewFollow">
                        <option value="euro" <?php echo ($viewFollow == 'euro') ? 'selected="selected"' : '';?>><?php echo  __($budget_settings, true)?></option>
                        <option value="man-day" <?php echo ($viewFollow == 'man-day') ? 'selected="selected"' : '';?>><?php echo  __($md, true)?></option>
                    </select>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div id="top-scroll">
                    <div></div>
                </div>
                <div class="wd-table" id="project_container" style="width:100%; height: 630px;">

                </div>
                <div id="pager" style="width:100%;height:0px; overflow: hidden;">

                </div>
            </div>
            <?php //echo $this->element('grid_status'); ?>
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
$columnDefaults = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'sortable' => false,
        'resizable' => false,
        'width' => 40,
        'noFilter' => 1,
    ),
    array(
    'rerenderOnResize' => true,
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 200,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1
    ),
    array(
        'id' => 'profit_center_id',
        'field' => 'profit_center_id',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Profit Center', true),
        'sortable' => false,
        'resizable' => false,
        'width' => 150,
        'noFilter' => 1,
    ),
    array(
        'id' => 'funder_id',
        'field' => 'funder_id',
        'name' => __d(sprintf($_domain, 'Internal_Cost'), 'Funder', true),
        'sortable' => false,
        'resizable' => false,
        'width' => 100,
        'noFilter' => 1,
    ),
    array(
    'rerenderOnResize' => true,
        'id' => 'budget',
        'field' => 'budget',
        'name' => ($viewFollow == 'euro') ? __('Budget', true). ' ' .  __($budget_settings, true) : __('Budget', true) . ' ' .  __($md, true),
        'width' => 130,
        'sortable' => true,
        'resizable' => true,
        'formatter' => 'Slick.Formatters.manDayValue',
        'noFilter' => 1
    )
);
$columnYears = $columnMonths = $listYears = $listMonths = array();
if($startDates != 0 && $endDates != 0 && $startDates <= $endDates){
    while($startDates <= $endDates){
        $_year = date('Y', $startDates);
        $_month = date('mY', $startDates);
        $key = strtotime(date('01-m-Y', $startDates));
        $columnYears[$_year] = array(
        'rerenderOnResize' => true,
            'id' => 'total_'.$_year,
            'field' => 'total_'.$_year,
            'name' => $_year,
            'width' => 130,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.manDayValue',
            'noFilter' => 1
        );
        $columnMonths[$_month] = array(
        'rerenderOnResize' => true,
            'id' => $key,
            'field' => $key,
            'name' => date('m-Y', $startDates),
            'width' => 130,
            'sortable' => true,
            'resizable' => true,
            'editor' => 'Slick.Editors.numericValueBudget',
            'validator' => 'DateValidate.valueMonth',
            'formatter' => 'Slick.Formatters.manDayValue',
            'noFilter' => 1
        );
        $listYears[$_year] = $_year;
        if(!in_array($key, $listMonths)){
            $listMonths[] = $key;
        }
        $startDates = mktime(0, 0, 0, date("m", $startDates)+1, date("d", $startDates), date("Y", $startDates));
    }
}
$columns = array();
$columns = array_merge($columnDefaults, $columnYears, $columnMonths);
$dataView = array();
$selectMaps = array();
$i = 1;
$totalBudget = 0;
$totalYears = array();
$headers = array();
if(!empty($internals)){
    $totalYearOfInternals = array();
    foreach($internals as $internal){
        $internal = $internal['ProjectBudgetInternalDetail'];
        $data = array(
            'id' => $internal['id'] . '_Internal',
            'no.' => $i++
        );
        $data['funder_id'] = isset($funders[$internal['funder_id']]) ? $funders[$internal['funder_id']] : '';
        $data['profit_center_id'] = isset($pcs[$internal['profit_center_id']]) ? $pcs[$internal['profit_center_id']] : '';
        $data['project_id'] = $projects['Project']['id'];
        $data['activity_id'] = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
        $data['name'] = (string) $internal['name'];
        $data['view'] = (string) $viewFollow;
        $data['budget'] = ($viewFollow == 'euro') ? (string) ((float)$internal['budget_md'] * (float) $internal['average']) : (string) $internal['budget_md'];
        $totalBudget += ($viewFollow == 'euro') ? (float) ((float)$internal['budget_md'] * (float) $internal['average']) : (float) $internal['budget_md'];
        if(!isset($headers['budget'])){
            $headers['budget'] = 0;
        }
        $headers['budget'] += ($viewFollow == 'euro') ? (float) ((float)$internal['budget_md'] * (float) $internal['average']) : (float) $internal['budget_md'];
        if(!empty($provisionals['Internal']) && !empty($provisionals['Internal'][$internal['id']])){
            foreach($provisionals['Internal'][$internal['id']] as $date => $value){
                if(!isset($headers[$date])){
                    $headers[$date] = 0;
                }
                $headers[$date] += !empty($value['value']) ? (float)$value['value'] : 0;
                $data[$date] = !empty($value['value']) ? $value['value'] : '';
                $years = date('Y', $date);
                if(!isset($totalYearOfInternals[$internal['id']][$years])){
                    $totalYearOfInternals[$internal['id']][$years] = 0;
                }
                $totalYearOfInternals[$internal['id']][$years] += $value['value'];
                if(!isset($totalYears[$years])){
                    $totalYears[$years] = 0;
                }
                $totalYears[$years] += $value['value'];
            }
        }
        if(!empty($listYears)){
            foreach($listYears as $listYear){
                $data['total_'.$listYear] = !empty($totalYearOfInternals[$internal['id']][$listYear]) ? $totalYearOfInternals[$internal['id']][$listYear] : 0;
                if(!isset($headers['total_'.$listYear])){
                    $headers['total_'.$listYear] = 0;
                }
                $headers['total_'.$listYear] += !empty($totalYearOfInternals[$internal['id']][$listYear]) ? (float)$totalYearOfInternals[$internal['id']][$listYear] : 0;
            }
        }
        $dataView[] = $data;
    }
}
if(!empty($externals)){
    $totalYearOfExternals = array();
    foreach($externals as $external){
        $external = $external['ProjectBudgetExternal'];
        $data = array(
            'id' => $external['id'] . '_External',
            'no.' => $i++
        );
        $data['funder_id'] = isset($funders[$external['funder_id']]) ? $funders[$external['funder_id']] : '';
        $data['profit_center_id'] = isset($pcs[$external['profit_center_id']]) ? $pcs[$external['profit_center_id']] : '';
        $data['project_id'] = $projects['Project']['id'];
        $data['activity_id'] = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
        $data['name'] = (string) $external['name'];
        $data['view'] = (string) $viewFollow;
        $data['budget'] = ($viewFollow == 'euro') ? (string) $external['budget_erro'] : (string) $external['man_day'];
        $totalBudget += ($viewFollow == 'euro') ? (float) $external['budget_erro'] : (float) $external['man_day'];
        if(!isset($headers['budget'])){
            $headers['budget'] = 0;
        }
        $headers['budget'] += ($viewFollow == 'euro') ? (float) $external['budget_erro'] : (float) $external['man_day'];
        if(!empty($provisionals['External']) && !empty($provisionals['External'][$external['id']])){
            foreach($provisionals['External'][$external['id']] as $date => $value){
                if(!isset($headers[$date])){
                    $headers[$date] = 0;
                }
                $headers[$date] += !empty($value['value']) ? (float)$value['value'] : 0;
                $data[$date] = !empty($value['value']) ? $value['value'] : '';
                $years = date('Y', $date);
                if(!isset($totalYearOfExternals[$external['id']][$years])){
                    $totalYearOfExternals[$external['id']][$years] = 0;
                }
                $totalYearOfExternals[$external['id']][$years] += $value['value'];
                if(!isset($totalYears[$years])){
                    $totalYears[$years] = 0;
                }
                $totalYears[$years] += $value['value'];
            }
        }
        if(!empty($listYears)){
            foreach($listYears as $listYear){
                $data['total_'.$listYear] = !empty($totalYearOfExternals[$external['id']][$listYear]) ? $totalYearOfExternals[$external['id']][$listYear] : 0;
                if(!isset($headers['total_'.$listYear])){
                    $headers['total_'.$listYear] = 0;
                }
                $headers['total_'.$listYear] += !empty($totalYearOfExternals[$external['id']][$listYear]) ? (float)$totalYearOfExternals[$external['id']][$listYear] : 0;
            }
        }
        $dataView[] = $data;
    }
}
$i18n = !empty($i18n) ? $i18n : array();
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var DateValidate = {};
    (function($){
        var $this = SlickGridCustom, gridControl,
        viewManDay = <?php echo json_encode($viewManDay);?>,
        viewEuro = <?php echo json_encode($viewEuro);?>,
        viewFollow = <?php echo json_encode($viewFollow);?>;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;

        var viewIcon = viewFollow == 'euro' ? viewEuro : viewManDay;
        // For validate date
        var projects = <?php echo json_encode($projects['Project']); ?>;
        var listYears = <?php echo json_encode($listYears);?>;
        var listMonths = <?php echo json_encode($listMonths);?>;
        var totalBudget = <?php echo  json_encode($totalBudget);?>;
        var totalYears = <?php echo json_encode($totalYears);?>;
        var totalHeaders = <?php echo json_encode($headers);?>;
        var getTime = function(value){
            value = value.split("-");
            return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
        }

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
                    if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
        var actionTemplate =  $('#action-template').html();
        $.extend(Slick.Formatters,{
            manDayValue : function(row, cell, value, columnDef, dataContext){
                if(value && value != 0){
                    value = number_format(value, 2, ',', ' ');
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + ' ' + viewIcon + '</span> ', columnDef, dataContext);
                } else {
                    value = (value && value != 0) ? value : '';
                    return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ', columnDef, dataContext);
                }
            }
        });

        DateValidate.valueMonth = function(value, args){
            var _valid = true;
            var _message = '';
            if(args && args.item){
                var total = parseFloat(value);
                var columnCurrent = args && args.column && args.column.field ? args.column.field : 0;
                $.each(args.item, function(ind, val){
                    ind = parseInt(ind);
                    if(!isNaN(ind) && ind != columnCurrent){
                        val = val ? val : 0;
                        total += parseFloat(val);
                    }
                });
                if(parseFloat(total) <= parseFloat(args.item.budget)){
                    _valid = true;
                } else {
                    _valid = false;
                    _message = $this.t('<?php echo __('The total Man Day less than or equal %1$s', true)?>', parseFloat(args.item.budget));
                }
            }
            return {
                valid : _valid,
                message : _message
            };
        }

        var  data = <?php echo json_encode($dataView); ?>;
        var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            project_id    : {defaulValue : projects['id'], allowEmpty : false},
            activity_id    : {defaulValue : projects['activity_id']},
            view : {defaulValue : ''}
        };
        if(listYears){
            $.each(listYears, function(ind, val){
                $this.fields['total_'+ind] = {defaulValue : ''};
            });
        }
        if(listMonths){
            $.each(listMonths, function(ind, val){
                $this.fields[val] = {defaulValue : ''};
            });
        }
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        var options = {
            //showHeaderRow: false,
            enableAddRow : false,
            frozenColumn: 4 + parseInt(Object.keys(listYears).length)
        };
        ControlGrid = $this.init($('#project_container'),data,columns,options);
        $this.onCellChange = function(args){
            if(args && args.column.field && args.item){
                var _totalYears = {};
                $.each(args.item, function(ind, val){
                    ind = parseInt(ind);
                    if(!isNaN(ind)){
                        var year = new Date(ind * 1000).getFullYear();
                        if(!_totalYears[year]){
                            _totalYears[year] = 0;
                        }
                        val = val ? val : 0;
                        _totalYears[year] += parseFloat(val);
                    }
                });
                if(_totalYears){
                    $.each(_totalYears, function(y, v){
                        args.item['total_'+y] = v.toFixed(2);
                    });
                }
                var keys = args.column.field;
                var total = 0;
                var yearHeaders = {};
                var _totalHeader = {};
                $.each(data, function(ind, val){
                    if(val[keys]){
                        total += parseFloat(val[keys]);
                    }
                    var _keys = Object.keys(val);
                    $.each(_keys, function(k, v){
                        v = parseInt(v);
                        if(!isNaN(v)){
                            if(!_totalHeader[v]){
                                _totalHeader[v] = 0;
                            }
                            _totalHeader[v] += val[v] ? parseFloat(val[v]) : 0;
                            var year = new Date(v * 1000).getFullYear();
                            if(!yearHeaders[year]){
                                yearHeaders[year] = 0;
                            }
                            var vl = val[v] ? val[v] : 0;
                            yearHeaders[year] += parseFloat(vl);
                        }
                    });
                });
                if(yearHeaders){
                    $.each(yearHeaders, function(y, v){
                        if(!_totalHeader['total_'+y]){
                            _totalHeader['total_'+y] = 0;
                        }
                        _totalHeader['total_'+y] += parseFloat(v);
                    });
                }
                if(_totalHeader){
                    $.each(_totalHeader , function(id){
                        var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + viewIcon : '';
                        if($(ControlGrid.getHeaderRowColumn(id)).hasClass('row-number')){
                            $(ControlGrid.getHeaderRowColumn(id)).find('.row-number b').html(val);
                        } else {
                            $(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
                        }
                    });
                }
            }
            $('.row-number').parent().addClass('row-number-custom');
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
        var totalMonths = {};
        $.each(data, function(ind, val){
            var keys = Object.keys(val);
            $.each(keys, function(k, v){
                v = parseInt(v);
                if(!isNaN(v)){
                    if(!totalMonths[v]){
                        totalMonths[v] = 0;
                    }
                    var vl = val[v] ? val[v] : 0;
                    totalMonths[v] += parseFloat(vl);
                }
            });
        });
        var cols = ControlGrid.getColumns();
        var numCols = cols.length;
        var gridW = 0;
        for (var i=0; i<numCols; i++) {
            gridW += cols[i].width;
        }
        // khi keo scroll thi to mau cac cell
        ControlGrid.onScroll.subscribe(function(e, args, scope){
            $('.row-parent').parent().addClass('row-parent-custom');
            $('.row-disabled').parent().addClass('row-disabled-custom');
            $('.row-number').parent().addClass('row-number-custom');

            $('#top-scroll').scrollLeft(args.scrollLeft);
        });
        ControlGrid.onColumnsResized.subscribe(function (e, args) {
            var _cols = ControlGrid.getColumns();
            var _numCols = cols.length;
            var _gridW = 0;
            for (var i=0; i<_numCols; i++) {
                _gridW += _cols[i].width;
            }
            $('#wd-header-custom').css('width', _gridW);
            refreshTopScroll();
        });
        header =
            '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">'
                + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                + '<div class="ui-state-default slick-headerrow-column l1 r1 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p></p></div>'
                + '<div class="ui-state-default slick-headerrow-column l2 r2 wd-row-custom wd-custom-cell gs-custom-cell-erro"><p>' + number_format(totalBudget, 2, ',', ' ') + ' ' + viewIcon + '</p></div>';
        var i = 3, j = 3;
        if(listYears){
            $.each(listYears, function(ind, val){
                var vl = totalYears[val] ? number_format(totalYears[val], 2, ',', ' ') + ' ' + viewIcon : '';
                header += '<div class="ui-state-default slick-headerrow-column l' + i + ' r' + j + ' wd-row-custom wd-custom-cell gs-custom-cell-erro" id="' + val + '"><p>' + vl + '</p></div>';
                i++;
                j++;
            });
        }
        if(listMonths){
            $.each(listMonths, function(ind, val){
                var vl = totalMonths[val] ? number_format(totalMonths[val], 2, ',', ' ') + ' ' + viewIcon : '';
                header += '<div class="ui-state-default slick-headerrow-column l' + i + ' r' + j + ' wd-row-custom wd-custom-cell gs-custom-cell-erro" id="' + val + '"><p>' + vl + '</p></div>';
                i++;
                j++;
            });
        }
        header += '</div>';
        //$('.slick-header-columns').after(header);
        if(totalHeaders){
            $.each(totalHeaders , function(id){
                var val = Number(this) ? number_format(Number(this), 2, ',', ' ') + ' ' + viewIcon : '';
                $(ControlGrid.getHeaderRowColumn(id)).html('<span class="row-number"><b>' + val + '</b></span>');
            });
        }
        $('.row-number').parent().addClass('row-number-custom');
        $('.slick-headerrow-columns div').addClass('gs-custom-cell-erro');
        $('#viewFollow').change(function(){
            location.href = '<?php echo $this->Html->url('/project_budget_provisionals/index/' . $project_id) ?>/' + $(this).val();
        });
        setTimeout(refreshTopScroll, 1000);
    })(jQuery);
    function refreshTopScroll(){
        /* top scroll */
        var rightPane = $('.slick-pane.slick-pane-top.slick-pane-right'),
            rightContent = $('.grid-canvas.grid-canvas-top.grid-canvas-right'),
            scroll = $('#top-scroll'),
            content = $('#top-scroll div');
        scroll.css({
            width: rightPane.width() + 'px',
            position: 'absolute',
            left: rightPane.offset().left + 'px',
            overflowX: 'scroll',
            overflowY: 'hidden',
            height: '25px',
            clear: 'both',
        }).off('scroll').on('scroll', function(e){
            var left = $(this).scrollLeft();
            $('.slick-viewport.slick-viewport-top.slick-viewport-right').scrollLeft(left);
        });
        content.css({
            width: rightContent.width() + 'px',
            height: '10px'
        });
        $('#project_container').css('margin-top', '35px');
    }
    //format float number
    function number_format(number, decimals, dec_point, thousands_sep) {
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
