<?php
echo $this->Html->css(array(
    'jquery.multiSelect',
    'slick_grid/slick.grid_v2',
    'slick_grid/slick.pager',
    'slick_grid/slick.common_v2',
    'slick_grid/slick.edit',
    '/js/qtip/jquery.qtip',
    'slick_grid/slick.grid',
    'preview/task_vision',
    'preview/grid-project',
));
echo $this->Html->script(array(
    'history_filter',
    'slick_grid/lib/jquery-ui-1.8.16.custom.min',
    'slick_grid/slick.core',
    'slick_grid/slick.dataview',
    'slick_grid/controls/slick.pager',
    'slick_grid/slick.formatters',
    'slick_grid/plugins/slick.cellrangedecorator',
    'slick_grid/plugins/slick.cellrangeselector',
    'slick_grid/plugins/slick.cellselectionmodel',
    'slick_grid/plugins/slick.dataexporter',
    'slick_grid/slick.editors',
    'slick_grid_custom',
    'qtip/jquery.qtip',
    'slick_grid/lib/jquery.event.drag-2.0.min',
    'slick_grid/slick.grid',
    'draw-progress'
));
echo $this->element('dialog_projects');
$employee_info = $this->Session->read("Auth.employee_info");
App::import("vendor", "str_utility");
$str_utility = new str_utility();

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
?>
<style>
   

</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-title">
                        <a href="javascript:void(0)" class="" id="refresh_menu" title="<?php __('Refresh')?>"><span></span></a>
                        <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen"></a>
                        <a href="<?php echo $this->Html->url(array('action' => 'export'));?>" id="export-table" class="btn btn-excel"><span></span></a>
                        <div class="filter-by-type">
                            <span class="filter-type type-green" data-type="green"></span>
                            <span class="filter-type type-red" data-type="red"></span>
                        </div>
                    </div>
                    <div class="wd-list-project">
                        <div class="wd-table project-list" id="project_container" style="width: 100%; height: 600px;">

                        </div>
                        <div id="pager" style="clear:both;width:100%;height:0;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
<div id="template_logs" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    
</div>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<?php
foreach($fieldset as $key => $_fieldset){
    if($key == 'Task'){
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 150,
            'class' => 'align-center-class',
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.linkFormatter'
        );
    } elseif($key == 'Status' ) {
        if($companyConfigs['diary_status']){
            $columns[] = array(
                'id' => $key,
                'field' => $key,
                'name' => $_fieldset,
                'width' => 100,
                'sortable' => true,
                'resizable' => true,
                'editor' => 'Slick.Editors.selectBox'
            );
        }else{
            $columns[] = array(
                'id' => $key,
                'field' => $key,
                'name' => $_fieldset,
                'width' => 100,
                'sortable' => true,
                'resizable' => true,
                // 'editor' => 'Slick.Editors.selectBox'
            );
        }
    } elseif( $key == 'Milestone' ) {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.milestoneFormatter'
        );
    } elseif ($key == 'Start' || $key == 'End') {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'datatype' => 'datetime',
            'formatter' => 'Slick.Formatters.taskImage'
        );
    } elseif ($key == 'Initial start' || $key == 'Initial end') {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'datatype' => 'datetime',
            'formatter' => 'Slick.Formatters.taskImage'
        );
    } elseif ($key == 'Workload') {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.taskImage'
        );
    } elseif ($key == 'Assigned') {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => false,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.taskImage'
        );
    } elseif ($key == 'Text') {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 200,
            'sortable' => false,
            'resizable' => true,
            'datatype' => 'number',
            'formatter' => 'Slick.Formatters.text'
        );
    } elseif ($key == 'Consume' || $key == 'Initial workload' || $key == 'Duration' || $key == 'Overload' || $key == 'In Used' || $key == 'Completed' || $key == 'Remain' || $key == 'Amount' || $key == 'Progress orderr' ) {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true,
            'formatter' => 'Slick.Formatters.valueFormat'
        );
    } else {
        $columns[] = array(
            'id' => $key,
            'field' => $key,
            'name' => $_fieldset,
            'width' => 100,
            'sortable' => true,
            'resizable' => true
        );
    }
}
$i = 1;
$dataView = array();
$selectMaps = array(
    'Status' => $listStatus,
    'Priority' => $listPriority,
    'Sub program' => $projectSubProgram,
    'Program' => $projectProgram,
    'Lot' => $listPartNames,
    'Phase' => $listPhaseNames,
    'Milestone' => $listMilestone
);
$i18n = array();
foreach ($datas as $id => $data) {
    $_data['Program'] = (string) !empty($data['amr_program']) ? $data['amr_program'] : '';
    $_data['Sub program'] = (string) !empty($data['sub_amr_program']) ? $data['sub_amr_program'] : '';
    $_data['Project name'] = (string) !empty($data['project_name']) ? $data['project_name'] : '';
    $_data['Lot'] = (string) !empty($data['part_name']) ? $data['part_name'] : '';
    $_data['Phase'] = (string) !empty($data['phase_name']) ? $data['phase_name'] : '';
    $_data['Task'] = (string) !empty($data['task_title']) ? $data['task_title'] : '';
    $_data['Status'] = (string) !empty($data['status']) ? $data['status'] : '';
    $_data['Milestone'] = (string) !empty($data['milestone']) ? $data['milestone'] : '';
    $_data['Priority'] = (string) !empty($data['priority']) ? $data['priority'] : '';
    $_data['Assigned'] = (string) !empty($data['assigned']) ? $data['assigned'] : '';
    $_data['Start'] = !empty($data['start_date']) ? $data['start_date'] : '';
    $_data['End'] = !empty($data['end_date']) ? $data['end_date'] : '';
    $_data['Workload'] = !empty($data['workload']) ? $data['workload'] : 0;
    $_data['Consume'] = !empty($data['consume']) ? $data['consume'] : 0;
    $_data['Code project'] = (string) !empty($data['code_project_1']) ? $data['code_project_1'] : '';
    $_data['Code project 1'] = (string) !empty($data['code_project_2']) ? $data['code_project_2'] : '';
    $_data['Text'] = (string) !empty($data['text']) ? $data['text'] : '';
    $_data['Initial workload'] = !empty($data['initial_estimated']) ? $data['initial_estimated'] : 0;
    $_data['Initial start'] = !empty($data['initial_task_start_date']) ? $data['initial_task_start_date'] : '';
    $_data['Initial end'] = !empty($data['initial_task_end_date']) ? $data['initial_task_end_date'] : '';
    $_data['Duration'] = !empty($data['duration']) ? $data['duration'] : 0;
    $_data['Overload'] = !empty($data['overload']) ? $data['overload'] : 0;
    $_data['In Used'] = !empty($data['in_used']) ? $data['in_used'] : 0;
    $_data['Completed'] = !empty($data['completed']) ? $data['completed'] . ' %' : 0 . ' %';
    $_data['Remain'] = !empty($data['remain']) ? $data['remain'] : 0;
    $_data['Amount'] = !empty($data['amount']) ? $data['amount'] . ' '.$bg_currency : 0 . ' '.$bg_currency;
    $_data['Progress order'] = !empty($data['progress_order']) ? $data['progress_order'] . ' %' : 0 . ' %';
    $_data['id'] = $id;
    $_data['idPr'] = $data['project_id'];
    $_data['no.'] = $id;
    $_data['DataSet'] = array();

    $dataView[] = $_data;
}
$myAvatar = '';
$avatarEmploys = array();
foreach ($listIdAva as $key => $value) {
    $avatarEmploys[$key] = $this->UserFile->avatar($value);
    if($value == $employee_id){
        $myAvatar = $avatarEmploys[$key];
    }
}
foreach ($listEmployeeRefer as $key => $value) {
    $listEmployeeRefer[$key]['avatar'] = $this->UserFile->avatar($key);
}
?>
<script type="text/javascript">
    var dataGrid;
    var DataValidator = {};
    var listIdModifyByPm = <?php echo json_encode($listIdModifyByPm) ?>;
    var clStatus = <?php echo json_encode($clStatus) ?>;
    var filter_color = <?php echo json_encode($filter_color) ?>;
    var listMilestone = <?php echo json_encode($listMilestone) ?>;
    var getEmpoyee = <?php echo json_encode($getEmpoyee) ?>;
    var _milestoneColor = <?php echo json_encode($_milestoneColor) ?>;
    var is_new_design = <?php echo json_encode( !empty( $employee_info['Color']['is_new_design']) ? 1 : 0 ) ?>;
    var _listIdModifyByPm = $.map(listIdModifyByPm, function(value, index) {
       return value;
    });
    var path = 'vision_task_filter_color';
    var listEditStatus = <?php echo json_encode($listEditStatus) ?>;
    var listEditStatus = $.map(listEditStatus, function(value, index) {
       return value;
    });
    var myId = <?php echo json_encode($employee_info['Employee']['id'])?>;
    var roleLogin = <?php echo json_encode($roleLogin);?>;
    var listEmployeeManagerOfT = <?php echo json_encode($listEmployeeManagerOfT) ?>;
    var fullName = <?php echo json_encode($fullName) ?>;
    var avatarEmploys = <?php echo json_encode($avatarEmploys) ?>;
    var listAssign = <?php echo json_encode($listAssign) ?>;
    var listEmployeeRefer = <?php echo json_encode($listEmployeeRefer) ?>;
    var myAvatar = <?php echo json_encode($myAvatar) ?>;
    var checkAvata = <?php echo json_encode($checkAvata) ?>;
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
    $('#refresh_menu').click(function(){
        $.ajax({
            url: '/activity_budget_externals/clean_filters/',
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
    if(filter_color == 'green'){
        $('.type-green').addClass('type-focus');
    } else if(filter_color == 'red'){
        $('.type-red').addClass('type-focus');
    }
    function expandScreen(){
        $('#table-control').hide();
        $('.wd-title').hide();
        $('#project_container').addClass('fullScreen');
        $('#collapse').show();
        $('.slick-viewport').css('height', '700px');
        $(window).resize();
    }
    function collapseScreen(){
        $('#table-control').show();
        $('.wd-title').show();
        $('#collapse').hide();
        $('#project_container').removeClass('fullScreen');
        $(window).resize();
    }
    function resizeHandler(){
        var _cols = dataGrid.getColumns();
        var _numCols = _cols.length;
        var _gridW = 0;
        for (var i=0; i<_numCols; i++) {
             _gridW += _cols[i].width;
        }
        $('#wd-header-custom').css('width', _gridW);
        $('#project_container').css('width', _gridW + 20);

    }
    (function($){

        $(function(){
            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            var actionTemplate =  $('#action-template').html();
            var actionTemplateEdit =  $('#action-template-edit').html();
            $.extend(Slick.Formatters,{
                linkFormatter : function(row, cell, value, columnDef, dataContext){
                    var idTask = dataContext.id ? dataContext.id : 0,
                        idPr = dataContext.idPr ? dataContext.idPr : 0;
                        task = dataContext.Task ? dataContext.Task : '';
                    return "<a href='/project_tasks/index/" + idPr + "/?id=" + idTask +"' target=_blank>" + task + "</a>";
                },
                text : function(row, cell, value, columnDef, dataContext){
                    // $('.slick-cell').css({'height':'42px', 'overflow-y': 'hidden', 'z-index' : 1});
                    if(getEmpoyee[dataContext.id] !== undefined){
                        first_name  =  getEmpoyee[dataContext.id]['first_name'].substr(0,1);
                        last_name  =  getEmpoyee[dataContext.id]['last_name'].substr(0,1);
                       return '<span class="project-manager-name circle-name" style="width: 30px; height: 30px; line-height: 30px; font-size: 14px;float: left;" title = "'+getEmpoyee[dataContext.id]['fullname']+'">'+ first_name +''+ last_name +'</span><p class="hover-popup">' + value + '</p><p class="open-popup"  onmouseover="openPopupText.call(this)" data-id = "'+ dataContext.id +'" onclick="getTaskText.call(this);">' + value + '</p>';
                    } else {
                        return '<p>' + value + '</p>';
                    }
                },
                taskImage : function(row, cell, value, columnDef, dataContext){
                    if(columnDef.field != 'Workload' && columnDef.field != 'Assigned'){
                        if(columnDef.field == 'Initial start' || columnDef.field == 'Initial end') {
                            return '<div style="text-align: center;"><span>' + value + '</span></div>';
                        }
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth()+1; //January is 0!
                        var yyyy = today.getFullYear();
                        dd = dd < 10 ? '0' + dd : dd;
                        mm = mm < 10 ? '0' + mm : mm;

                        curDate = new Date(yyyy + '-' + mm + '-' + dd).getTime();
                        var sDate = value.split('-');
                        sDate = new Date(sDate[2] + '-' + sDate[1] + '-' + sDate[0]).getTime();
                        if(columnDef.field == 'Start'){
                            if(value){
                                var _classDate = 'task_blue';
                                var _title = '';
                                if(dataContext.Consume == 0 && sDate < curDate && dataContext.Workload > 0 && dataContext.Status != clStatus){
                                    _classDate = 'task_red';
                                    _title = '<?php echo __("No consumed and the current date > start date") ?>';
                                }
                                return '<div style="text-align: center;"><span class="' + _classDate + '" title="' + _title + '">' + value + '</span></div>';
                            } else {
                                return '<div style="text-align: center;"><span>' + value + '</span></div>';
                            }
                        } else if(columnDef.field == 'End'){
                            if(value){
                                var _classDate = 'task_blue';
                                var _title = '';
                                if(dataContext.Status  && dataContext.Status != clStatus && sDate < curDate){
                                    _classDate = 'task_red';
                                    _title = '<?php echo __("Task not closed and the current date > end date") ?>';
                                }
                                return '<div style="text-align: center;"><span class="' + _classDate + '" title="' + _title + '">' + value + '</span></div>';
                            } else {
                                return '<div style="text-align: center;"><span>' + value + '</span></div>';
                            }
                        }
                    }else if(columnDef.field == 'Assigned'){
                        var circle_ava = '';
                        $.each(listAssign, function(key, _assign) {
                            if(_assign['ProjectTaskEmployeeRefer']['project_task_id'] == dataContext.id ){
                                if(_assign['ProjectTaskEmployeeRefer']['is_profit_center'] == 1){
                                    circle_ava += '<a class="circle-name" title = "'+ listEmployeeRefer[_assign['ProjectTaskEmployeeRefer']['reference_id']]['employee_name']+'"><i class="icon-people"></i></a>';
                                }else{
                                    if(checkAvata[_assign['ProjectTaskEmployeeRefer']['reference_id']]){
                                        circle_ava += '<a class="circle-name" title = "'+ listEmployeeRefer[_assign['ProjectTaskEmployeeRefer']['reference_id']]['employee_name']+'"><img src = "'+ listEmployeeRefer[_assign['ProjectTaskEmployeeRefer']['reference_id']]['avatar']+'"></a>';
                                    }else{
                                        employee_name = listEmployeeRefer[_assign['ProjectTaskEmployeeRefer']['reference_id']]['employee_name'].split(" ");
                                        first_name  =  employee_name[0].substr(0,1);
                                        last_name  =  employee_name[1].substr(0,1);
                                        circle_ava += '<a class="circle-name" title = "'+ listEmployeeRefer[_assign['ProjectTaskEmployeeRefer']['reference_id']]['employee_name']+'"><span>'+first_name+''+last_name+'</span></a>';

                                    }
                                }
                            }
                        });
                        return circle_ava;
                    }else {
                        var _classDate = 'task_blue';
                        var _title = '';
                        if(parseFloat(value) < parseFloat(dataContext.Consume)){
                            _classDate = 'task_red';
                            _title = '<?php echo __("Consumed > Workload") ?>';
                        }
                        return '<div style="margin-left: 30px;"><span class="' + _classDate + '" title="' + _title + '">' + parseFloat(value).toFixed(2) + ' J.H</span></div>';
                    }
                },
                valueFormat: function(row, cell, value, columnDef, dataContext){
					console.log(value);
                    return '<div style="text-align: right;"><span>' + parseFloat(value).toFixed(2) + '</span></div>';
                },
                workloadFormat: function(row, cell, value, columnDef, dataContext){
                    console.log(parseFloat(value).toFixed(2));
                    return '<div style="text-align: right;"><span>' + parseFloat(value).toFixed(2) + ' J.H</span></div>';
                },
                milestoneFormatter: function(row, cell, value, columnDef, dataContext){
                    if( value && listMilestone[value] ){
                        return '<div><i class="' + _milestoneColor[value] + '" style="width: 25px; float: left; margin-top: 5px;">&nbsp</i><span style="margin-left: -25px">' + listMilestone[value] + '</span></div>';
                    } else {
                        return '';
                    }
                }
            });
            $.extend(Slick.Editors,{
                text: function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
                    var check = false;
                    if(listEmployeeManagerOfT[args.item.id] !== undefined) check = true;
                    var _assign = args.item.Assigned.split(', ');
                    if(_assign.length > 0){
                        for (var i = 0; i < _assign.length; i++) {
                            if(_assign[i] == fullName) check = true;
                        }
                    }
                    if(check){
                        $(args.container).css({'height': '70px', 'z-index': 99999, 'overflow-y' : 'auto'});
                        if(avatarEmploys[args.item.id] !== undefined){
                            this.input = $("<input style='height:13px' type=text class='editor-text' /><img style='width: 20px; height: 20px; float: left; margin-right: 5px' src='"+avatarEmploys[args.item.id]+"'><p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        } else {
                            this.input = $("<input style='height:13px' type=text class='editor-text' /><p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        }
                        this.focus();
                        var $input = this.input[0];
                        var defaultValue = args.item.Text;
                        this.setValue = function (val) {
                            $($input).val('');
                        };
                        this.isValueChanged = function () {
                            if( $($input).val() != "" && $($input).val() != defaultValue ){
                                avatarEmploys[args.item.id] = myAvatar;
                            }
                            return $($input).val() != "" && $($input).val() != defaultValue;
                        };
                    } else {
                        if(avatarEmploys[args.item.id] !== undefined){
                            this.input = $("<img style='width: 25px; height: 25px; float: left; margin-right: 5px' src='"+avatarEmploys[args.item.id]+"'><p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        } else {
                            this.input = $("<p>" + args.item.Text +"</p>")
                            .appendTo(args.container);
                        }
                    }
                }
            });
            $this.onBeforeEdit = function(args){
                if(args.column.id == 'Status'){
                    if(roleLogin == 'pm'){
                        if((args.item.idPr && $.inArray(args.item.idPr, _listIdModifyByPm) != -1) || $.inArray( myId, listEditStatus) != -1 ){
                            // do nothing
                        } else {
                            return false;
                        }
                    }
                    else if($.inArray( myId, listEditStatus) != -1 ){
                        // do nothing
                    }
               }
               return true;
            }
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                Status : {defaulValue : ''},
                Text : {defaulValue : ''},
            };
             var options = {
                headerRowHeight: 42,
                rowHeight: 42,
                enableAddRow: false
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update_vision_task')); ?>';
            dataGrid = $this.init($('#project_container'),data,columns, options);

            $('.filter-type').on('click', function(){
                var which = $(this).data('type');
                if( $(this).hasClass('type-focus') ){
                    $('.filter-type').removeClass('type-focus');
                    which = '';
                } else {
                    $('.filter-type').removeClass('type-focus');
                    $(this).addClass('type-focus');
                }
                $.ajax({
                    url: '/projects/saveFilterSortVisionTask',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        which: which
                    },
                    success: function(data){
                        location.reload(true);
                    }
                });
            });
            dataGrid.onColumnsResized.subscribe(function (e, args) {
                resizeHandler();
            });

            history_reset = function(){
                var _cols = dataGrid.getColumns();
                var _numCols = _cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                     _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('#project_container').css('width', _gridW + 20);
            };
            var exporter = new Slick.DataExporter('/projects/export');
            dataGrid.registerPlugin(exporter);
            $('#export-table').click(function(){
                exporter.submit();
                return false;
            });
            
            $(window).resize(function(){
                dataGrid.resizeCanvas();
            });
        });
    })(jQuery);

        // hover open popup text
    function openPopupText(){
        $('.hover-popup').removeClass('open');
        $('.slick-cell').removeClass('active');
        $(this).closest('.slick-cell').addClass('active');
        $(this).closest('.slick-cell').find('.hover-popup').addClass('open');
    }
    var updateTaskText = function(){
        var text = $(this).val(),
        id = $(this).data("id");
        if(text != ''){
            var _html = '';
            $.ajax({
                url: '/projects_preview/updateTaskText',
                type: 'POST',
                data: {
                    data:{
                        id: id,
                        text: text,
                    }
                },
                success: function(data) {
                    border = 'red';
                    if(data){
                        border ='#32B04D';
                        $('#update-comment').css( "border-color", border);
                    } 
                }
            });
        }
    };
    function getTaskText() {
        id = $(this).data("id");
        var _html = '';
        var latest_update = '';
        var popup = $('#template_logs');
        $.ajax({
            url: '/projects_preview/getProjectTaskText',
            type: 'POST',
            data: {
                id: id,
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                _html = '<p class="project-name">'+data['project_name']+'</p>';
                html = '<div id="content-comment-id">';
                if (data) {
                    latest_update = 'Modifié le ' + data[0]['created'] +' par '+ data['update_by_employee'];
                    i = 0
                    _html += '<div class="content-logs">'
                    $.each(data, function(ind, _data) {
                        if(_data && ind < 3){
                            comment = _data['comment'] ? _data['comment'].replace(/\n/g, "<br>") : '';
                            date = _data['created'];
                              if(i == 0){
                                _html += '<div class="content content-'+ i++ +'"><p>'+ date +'</p><div class="comment"><textarea onfocusout="updateTaskText.call(this)" data-id = '+ id +' cols="30" rows="4" id="update-comment"></textarea></div></div>';
                            } _html += '<div class="content content-'+ i++ +'"><p>'+ date +'</p><div class="comment">'+ comment +'</div></div>';
                        } 
                        i++;                       
                    });
                    _html += '</div>'
                } else {
                    _html += '';
                }
                var class_pro ='';
                if(data['initDate'] > 50) class_pro = 'late-progress';
                _html_progress ="<div class='log-progress'><div class='project-progress "+ class_pro +"'><p class='progress-full'>" + draw_line_progress(data['initDate']) + "</p><p>"+ data['initDate'] +"%</p></div>";
                _html+= _html_progress;
                _html += '<div class="logs-info"><p class="update-by-employee">'+ latest_update +'</p></div></div></div>';
                $('#content_comment').html(_html);
                
                var createDialog2 = function(){
                    $('#template_logs').dialog({
                        position    :'center',
                        autoOpen    : false,
                        height      : 420,
                        modal       : true,
                        width       : 320,
                        minHeight   : 50,
                        open : function(e){
                            var $dialog = $(e.target);
                            $dialog.dialog({open: $.noop});
                        }
                    });
                    createDialog2 = $.noop;
                }
                createDialog2();
                $("#template_logs").dialog('option',{title:''}).dialog('open');
                
            }
        });
       
    };
</script>
