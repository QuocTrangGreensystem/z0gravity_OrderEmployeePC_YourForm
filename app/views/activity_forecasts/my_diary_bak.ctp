<?php echo $html->css(array('projects','activity_forecast_my_diary')); ?>
<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php echo $html->css('button_custom'); ?>  
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'absence_requests', 'action' => 'export')));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("My Diary", true); ?></h2>
                    <?php /* <a href="javascript:void(0);" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a> */ ?>	
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $am = __('AM', true);
                    $pm = __('PM', true);

                    $dayMaps = array(
                        'monday' => $_start,
                        'tuesday' => $_start + DAY,
                        'wednesday' => $_start + (DAY * 2),
                        'thursday' => $_start + (DAY * 3),
                        'friday' => $_start + (DAY * 4),
                        'saturday' => $_start + (DAY * 5),
                        'sunday' => $_start + (DAY * 6)
                    );
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <?php
                        echo $this->element('week');
                        ?>
                        <div id="table-control">
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                            <fieldset>
                                <h3 class="input"><?php __('You are view in :'); ?></h3>
                                <div class="input">
                                    <?php
                                    echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false));
                                    ?>
                                </div>
                                <div class="input">
                                    <?php
                                    echo $this->Form->month('month', date('m', $_start), array('empty' => false));
                                    ?>
                                </div>
                                <div class="input">
                                    <?php
                                    echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false));
                                    ?>
                                </div>
                                <div class="button">
                                    <input type="submit" value="OK" />
                                </div>
                                <div style="clear:both;"></div>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                        <div id="absence-wrapper">
                            <table id="absence">
                                <thead>
                                    <tr>
                                        <th rowspan="2"><?php __('Employee'); ?></th>
                                        <th rowspan="2"><?php __('Workload*/Capacity*'); ?></th>
                                        <th colspan="2"><?php echo __('Monday', true) . ' / ' . date('d M', $dayMaps['monday']); ?></th>
                                        <th colspan="2"><?php echo __('Tuesday', true) . ' / ' . date('d M', $dayMaps['tuesday']); ?></th>
                                        <th colspan="2"><?php echo __('Wednesday', true) . ' / ' . date('d M', $dayMaps['wednesday']); ?></th>
                                        <th colspan="2"><?php echo __('Thursday', true) . ' / ' . date('d M', $dayMaps['thursday']); ?></th>
                                        <th colspan="2"><?php echo __('Friday', true) . ' / ' . date('d M', $dayMaps['friday']); ?></th>
                                        <th colspan="2"><?php echo __('Saturday', true) . ' / ' . date('d M', $dayMaps['saturday']); ?></th>
                                        <th colspan="2"><?php echo __('Sunday', true) . ' / ' . date('d M', $dayMaps['sunday']); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                        <th><?php echo $am; ?></th>
                                        <th><?php echo $pm; ?></th>
                                    </tr>
                                </thead>
                                <tbody id="absence-table">
                                    <tr><td colspan="15">&nbsp;</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$dataView = array();
foreach ($employees as $id => $employee) {
    foreach ($dayMaps as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'activity_am' => 0,
            'activity_pm' => 0,
            'employee_id' => $id
        );
        foreach (array('am', 'pm') as $type) {
            if (!empty($requests[$id][$time]['absence_' . $type])
                    && ($requests[$id][$time]['response_' . $type] === 'validated'
                    || empty($forecasts[$id][$time]['activity_' . $type]))) {
                $default['absence_' . $type] = $requests[$id][$time]['absence_' . $type];
                $default['response_' . $type] = $requests[$id][$time]['response_' . $type];
            }
            if (!empty($forecasts[$id][$time]['activity_' . $type])) {
                $default['activity_' . $type] = $forecasts[$id][$time]['activity_' . $type];
                $default[$type . '_model'] = strtolower($forecasts[$id][$time][$type . '_model']);
            }
        }
        $dataView[$id][$day] = $default;
    }
}
if(!empty($workloads)){
    foreach($dataView as $id1 => $_dataViews){
        foreach($_dataViews as $time1 => $_dataView){
            foreach($workloads as $id2 => $_workloads){
                foreach($_workloads as $time2 => $_workload){
                    if($id1 == $id2 && $time1 == $time2){
                        $dataView[$id1][$time1]['workload'] = $_workload;
                    }
                }
            }    
        }
    }
}
$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Summary' => __('Summary', true),
    'Holiday' => __('Holiday', true),
    'Unknown' => __('Unknown', true),
    'Requested' => __('Requested', true),
    'Validated' => __('Validated', true),
    'No name' => __('No name', true),
    'Detail of %s' => __('Detail of %s', true),
    'Remove forecast' => __('Remove forecast', true),
);
$css = '';
foreach ($constraint as $key => $data) {
    $css .= ".rp-$key span {background-color : {$data['color']};}";
}
echo '<style type="text/css">' . $css . '</style>';
?>
<div style="display: none;" id="message-template">
    <div class="message error"><?php echo __('Cannot connect to server ...', true); ?><a href="#" class="close">x</a></div>
</div>
<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="buttons" style="display: none;" title="<?php echo __('Add new comments', true) ?>">
    <fieldset>
        <textarea rel="no-history" name="comment"></textarea>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="ok"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<div id="tooltip-template" class="buttons" style="display: none;">
    <dl id="tooltip-template-dl">
        <dt><?php __('Short name'); ?> :</dt>
        <dd>%1$s</dd>
        <dt><?php __('Long name'); ?> :</dt>
        <dd>%2$s</dd>
        <dt><?php __('Family'); ?> :</dt>
        <dd>%3$s</dd>
        <dt><?php __('Subfamily'); ?> :</dt>
        <dd>%4$s</dd>
    </dl>
</div>
<style type="text/css">
    .list-comment .info{
        background-color: #185790;
        margin-bottom: 3px;
        color: #fff;
    }
    .list-comment .info .comment{
        font-size: 0.9em;
    }
    #tooltip-template-dl{
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
    #tooltip-template-dl dt,#tooltip-template-dl dd{
        float: left;
        padding: 0 5px;
        margin: 0;
    }
    #tooltip-template-dl dt{
        clear: left;
        width: 80px;
    }
    .wd-dis-play{
        display: none !important;
    }
</style>
<script type="text/javascript">
    (function($){
        
        
        $(function(){
        
            var updateUrl = <?php echo html_entity_decode(json_encode($this->Html->url(array('?' => array('week' => date('W', $_end), 'year' => date('Y', $_end)), 'action' => 'update', $profit['id'])))); ?>,
            dataSets = <?php echo json_encode($dataView); ?>,
            holidays = <?php echo json_encode(@$holidays); ?> || {},
            absences = <?php echo json_encode($absences); ?>,
            workdays = <?php echo json_encode($workdays); ?>,
            mapeds = <?php echo json_encode($mapeds); ?>,
            employees = <?php echo json_encode($employees); ?>,
            employeeName = <?php echo json_encode($employeeName); ?>,
            $workloads = <?php echo json_encode($workloads); ?>,            
            requestConfirms = <?php echo json_encode($requestConfirms); ?>,
            $container = $('#absence-table').html('');
            /* --------Comment--------- */
            var checkActivity = function(type,dx){
                return !!(dx && dx[type+ '_model'] == 'activity');
            };
            var updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
            deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
            comments = <?php echo json_encode(@$comments); ?> || {},
            userComments = <?php echo json_encode(@$userComments); ?> || {};
            /**
             * Translate strings to the page language or a given language.
             */
            var i18ns = <?php echo json_encode($i18ns); ?>;
            var format = function(str,args) {
                var regex = /%(\d+\$)?(s)/g,
                i = 0;
                return str.replace(regex, function (substring, valueIndex, type) {
                    var value = valueIndex ? args[valueIndex.slice(0, -1)-1] : args[i++];
                    switch (type) {
                        case 's':
                            return String(value);
                        default:
                            return substring;
                    }
                });
            };
            var t = function (str,args) {
                
                if (i18ns[str]) {
                    str = i18ns[str];
                }
                if(args === undefined){
                    return str;
                }
                if (!$.isArray(args)) {
                    args = $.makeArray(arguments);
                    args.shift();
                }
                return format(str, args);
            };
            var parseHandler = function(callback , $list ,  data){
                $('#message-place').html(data.message);
                setTimeout(function(){
                    $('#message-place .message').fadeOut('slow');
                } , 5000);
                callback($list , data);
            };
            var syncHandler = function(args , dsubmit , callback , check){
                var submit = {}, $list = $(this).find('td.ui-selected');
                $list.each(function(){
                    var $el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')];
                    if(!_ds || $el.hasClass('loading') || ($.isFunction(check) && check($el) === false)){
                        return;
                    }
                    if(!submit[_ds.employee_id]){
                        submit[_ds.employee_id] = {};
                    }
                    if(!submit[_ds.employee_id][_ds.date]){
                        submit[_ds.employee_id][_ds.date] = {
                            date: _ds.date,
                            employee_id : _ds.employee_id
                        };
                    }
                    submit[_ds.employee_id][_ds.date][$el.hasClass('am') ? 'am' : 'pm'] = args.value;
                    $el.addClass('loading');
                });
                if(!$.isEmptyObject(submit)){
                    $.ajax({
                        url : args.url,
                        cache : false,
                        type : 'POST',
                        dataType : 'json',
                        data : {
                            data : $.extend(dsubmit,submit)
                        },
                        success : function(data){
                            parseHandler(callback, $list, data);
                        },
                        error : function(){
                            parseHandler(callback , $list, {
                                error : true,
                                message : $('#message-template').html()
                            });
                        }
                    });
                }
            };
            /* --------Custom--------- */
            var absenceHandler = function(data , model){
                syncHandler.call( this ,{ value : true, url :  updateUrl} , {
                    model : model,
                    request : data
                } , function($list , data){
                    $list.each(function(){
                        var type,$el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')];
                        if(data[_ds.employee_id]){
                            var res = data[_ds.employee_id][_ds.date];
                            if(res && res.result){
                                _ds = $.extend(_ds , res || {});
                                type = _ds['activity_' + ($el.hasClass('am') ? 'am' : 'pm')];
                                if(model == 'task'){
                                    // mai lam tiep..met qua
                                } else {
                                    $el.find('span').html(type && type != '0' ? ((mapeds[model][type] || {name : 'unknown'}).name) : '0.5');
                                }
                            }
                        }
                        $el.removeClass('loading');
                    });
                }, function($el){
                    return !(data == '0' && $el.find('span').text() == '0.5');
                });
            };
            
            /* --------Draw table--------- */
            var dataSum = {total : 0}, sumText = '', status = {
                0 : t('Requested'),
                2 : t('Validated')
            };
            var activityFromTasks = <?php echo json_encode($activities); ?>;
            var projectFromTasks = <?php echo json_encode($projects); ?>;
            var phaseFromTasks = <?php echo json_encode($projectPhases); ?>;
            var partFromTasks = <?php echo json_encode($projectParts); ?>;
            
            $.each(dataSets, function(id){
                var output = _output = '',total = _tForecast = _tRemain = _tWorkload = 0,select = $.type(requestConfirms[id]) == 'undefined', _workloads = [];
                $.each(this ,function(day , data){
                    var val = parseFloat(workdays[day]),dt = holidays[data.date] || {},
                    opt = {am : {className : ['am',day] , value : 0}, pm : {className : ['pm',day] , value : 0}} ;
                    _opt = {am : {className : ['am',day] , rWork : []}, pm : {className : ['pm',day] , rWork : []}} ;
                    
                    if(this.workload){
                        var _workAm = _workPm = _arrProject = _arrPhase = _arrActivity = [];
                        var objProject = new Object();
                        $.each(this.workload ,function(key , _data){
                            var remain_am = remain_pm = 0;
                            var namGroupAm = namGroupPm = '';
                            if(key == 'am'){
                                $.each(_data, function(indexs, vl){
                                    if(indexs != 'display'){
                                        remain_am += parseFloat(vl.remain);
                                    }
                                });
                                if(_data.display.activity){
                                    $.each(_data.display.activity, function(idAc, nameAc){
                                        var nameActivity = activityFromTasks[idAc] ? activityFromTasks[idAc] : '';
                                        namGroupAm += '<p style="font-weight: bold;">' +activityFromTasks[idAc]+ '</p><p>' +nameAc+ '</p>';
                                    });
                                }
                                if(_data.display.project){
                                    $.each(_data.display.project, function(idPr, IdParts){
                                        var nameProject = projectFromTasks[idPr] ? projectFromTasks[idPr] : '';
                                        namGroupAm += '<p style="font-weight: bold;">' +nameProject+ '</p>';
                                        $.each(IdParts, function(idPart, IdPhases){
                                            var namePart = partFromTasks[idPart] ? partFromTasks[idPart] : '';
                                            namGroupAm += '<p style="font-weight: bold; color: #5A3FE7;">' +namePart+ '</p>';
                                            $.each(IdPhases, function(idPh, namePr){
                                                var namePhase = phaseFromTasks[idPh] ? phaseFromTasks[idPh] : '';
                                                namGroupAm += '<p style="color: #6529EA;">' +namePhase+ '</p><p>' +namePr+ '</p>';
                                            });
                                        });
                                    });
                                }
                            }
                            if(key == 'pm'){
                                $.each(_data, function(indexs, vl){
                                    if(indexs != 'display'){
                                        remain_pm += parseFloat(vl.remain);
                                    }
                                });
                                if(_data.display.activity){
                                    $.each(_data.display.activity, function(idAc, nameAc){
                                        var nameActivity = activityFromTasks[idAc] ? activityFromTasks[idAc] : '';
                                        namGroupPm += '<p style="font-weight: bold;">' +activityFromTasks[idAc]+ '</p><p>' +nameAc+ '</p>';
                                    });
                                }
                                if(_data.display.project){
                                    $.each(_data.display.project, function(idPr, IdParts){
                                        var nameProject = projectFromTasks[idPr] ? projectFromTasks[idPr] : '';
                                        namGroupPm += '<p style="font-weight: bold;">' +nameProject+ '</p>';
                                        $.each(IdParts, function(idPart, IdPhases){
                                            var namePart = partFromTasks[idPart] ? partFromTasks[idPart] : '';
                                            namGroupPm += '<p style="font-weight: bold; color: #5A3FE7;">' +namePart+ '</p>';
                                            $.each(IdPhases, function(idPh, namePr){
                                                var namePhase = phaseFromTasks[idPh] ? phaseFromTasks[idPh] : '';
                                                namGroupPm += '<p style="color: #6529EA;">' +namePhase+ '</p><p>' +namePr+ '</p>';
                                            });
                                        });
                                    });
                                }
                            }
                            _setValueAm = namGroupAm;
                            _setValuePm = namGroupPm;
                            _tRemain += remain_am+remain_pm;
                            _workloads['remain'] = _tRemain;
                            dataSum[id] = _workloads;
                            if(_setValueAm){
                                 _opt['am'].rWork.push(_setValueAm);
                            }
                            if(_setValuePm){
                                _opt['pm'].rWork.push(_setValuePm);
                            }
                            _opt['am'].className.push('selectable');
                            _opt['pm'].className.push('selectable');
                        });
                    } else {
                         _opt['am'].rWork.push(0);
                         _opt['pm'].rWork.push(0);
                    }
                    switch(val){ 
                        case 1:
                            if(!dt['am']){
                                select && opt['am'].className.push('selectable');
                            }else{
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                            if(!dt['pm']){
                                select && opt['pm'].className.push('selectable');
                            }else{
                                opt['pm'].className.push('rp-holiday');
                                opt['pm'].value = t('Holiday');
                            }
                            break;
                        case 0.5:
                            if(!dt['am']){
                                select && opt['am'].className.push('selectable');
                            }else{
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                    }
                    $.each(['am','pm'] , function(){
                        try {
                            if(checkActivity(this,data) || comments[data.employee_id][data.date][this]){
                                opt[this].className.push('has-comment');
                            }
                        }catch(ex){};
                  
                        if(data['absence_' +  this] && data['response_' +  this] == 'validated'){
                            if(absences[data['absence_' +  this]]){
                                opt[this].value = absences[data['absence_' +  this]].print;
                            }
                            opt[this].className = [day, this,'rp-validated'];
                        }else{
                            switch(true){ 
                                case val == 0.5 && this == 'am' && !dt['am']:
                                    total += 0.5;
                                    opt['am'].className.push('workday');
                                    opt['am'].value = 0.5;
                                    break;
                                case val == 1 && !dt[this]:
                                    total += 0.5;
                                    opt[this].className.push('workday');
                                    opt[this].value = 0.5;
                                    break;
                            }
                        }
                        if(data['activity_' +  this]){
                            dataSum[day+this]+=0.5;
                            if(mapeds[data[this+ '_model']][data['activity_' +  this]]){
                                if(data[this+ '_model'] == 'task'){
                                    opt[this].value = 0;
                                    opt[this].className.splice(opt[this].className.indexOf("selectable"),1);
                                    opt[this].className.splice(opt[this].className.indexOf("ui-selectee"),1);
                                } else {
                                    _tForecast += 0.5;
                                    opt[this].value = mapeds[data[this+ '_model']][data['activity_' +  this]].name;
                                }
                                _workloads['forecast'] = _tForecast;
                                dataSum[id] = _workloads;
                            }else{
                                opt[this].value = t('Unknown');
                            }
                        }
                        if(!dataSum[day+this]){
                            dataSum[day+this] = 0;
                        }
                        if($.isNumeric(opt[this].value)){
                            dataSum[day+this] += opt[this].value;
                        }
                        if(data['absence_' +  this] && data['response_' +  this] != 'validated'){
                            if(absences[data['absence_' +  this]]){
                                opt[this].value = absences[data['absence_' +  this]].print;
                            }
                            opt[this].className.push('rp-' + data['response_' +  this]);
                        }
                    });
                    $.each(opt, function(){
                        output += '<td dx="' + id + '" dy="' + day + '" class="wd-fore ' + this.className.join(' ') +'"><span>' + this.value + '</span></td>';
                    });
                    if(_opt){
                        $.each(_opt, function(){
                            _rWork = this.rWork.toString().replace(',', '<br />');                                                     
                            _output += '<td dx="' + id + '" dy="' + day + '" class="wd-work ' + this.className.join(' ') +'"><span>' + _rWork + '</span></td>';
                        });
                    }
                });
                if(dataSum[id]){
                    if(dataSum[id].forecast){
                        if(dataSum[id].remain){
                            dataSum[id] = dataSum[id].forecast + dataSum[id].remain;
                        } else {
                            dataSum[id] = dataSum[id].forecast + 0;
                        }
                    } else {
                        if(dataSum[id].remain){
                            dataSum[id] = dataSum[id].remain + 0;
                        } else {
                            dataSum[id] = dataSum[id].forecast + 0;
                        }
                    }
                }
                dataSum.total += total; 
                dataSum[id] = dataSum[id] ? dataSum[id].toFixed(2) : 0;
                var $class = '';
                if(dataSum[id] > total){
                    $class = 'check-workload';
                }
                $container.append('<tr><td class="st" rowspan="2"><span>' + employees[id] 
                    + (select ? '' : ('<strong> (' + status[requestConfirms[id]] + ')</strong>')) 
                    + '</span><td class="ct ' + $class + '" rowspan="2"><span>' + dataSum[id] + '/' + total + '</span></td>' + output + '</tr>'
                    + '<tr>' + _output + '</tr>'
                    );
            });
            var _eWorkload = 0;
            $.each(dataSum , function(i){
                if(!isNaN(i)){
                    _eWorkload += parseFloat(dataSum[i]);
                }
            });
            sumText += '<tr class="space"><td colspan="16"><span>&nbsp;</span></td></tr>';
            var $_class = '';
            _eWorkload = _eWorkload.toFixed(2);
            if(_eWorkload > dataSum.total){
                $_class = 'check-workload';
            }
            sumText += '<tr class="summary"><td class="ct"><span>' + t('Summary') + '</span><td class="ct ' + $_class + '"><span>' + _eWorkload + '/' + dataSum.total + '</span></td>';
            delete dataSum.total;
            $.each(dataSum , function(i){
                if(!isNaN(i)){
                    delete dataSum[i];
                } else {
                   sumText += '<td class=""><span>' + this + '</span></td>'; 
                }
            });
            $container.append(sumText + '</tr>');
            
            var contextMenu = {hide : $.noop},removeTooltip = function(not){
                $container.find('.xtip-show').not(not).trigger('xtip-hide').removeClass('xtip-show');
            };
            
            $container.selectable({
                filter : 'td.selectable',
                selecting:function(){
                    removeTooltip();
                },
                unselected : function(){
                    contextMenu.hide();
                }
            });
            
            /* --------Comment--------- */
            var tooltipTemplate = $('#tooltip-template').html();
            var infoActivity = function($el,type,dx){
                var $list = $(this).find('ul.list-comment');
                var $info = $list.find('.info-comment').html('');
                if(!$info.length){
                    $info = $('<div class="comment info-comment"></div>');
                    var $del = $('<a class="close" title="'+ t('Close') +'">x</a>').click(function(){
                        $(this).closest('li').hide();
                        if($list.children().length == 1){
                            $el.removeClass('has-comment');
                            $el.tooltip('close');
                            $el.tooltip('disable');
                        }
                        return false;
                    });
                    $list.prepend($(t('<li class="info"><h4 class="title">%s</h4></li>' , t('Detail of %s' , dx.name))).append($del).append($info));
                }
                $info.html(t(tooltipTemplate,dx.short_name
                ,dx.long_name,(mapeds['family'][dx.family_id] || {name : ''}).name,(mapeds['subfamily'][dx.subfamily_id] || {name : ''}).name));
            };
            var removeComment = function($el,id){
                $.ajax({
                    url : deleteUrl2,
                    cache : false,
                    type : 'GET',
                    data : {
                        id : id
                    }
                });
                if(this.siblings().length == 0){
                    $el.removeClass('has-comment');
                    $el.tooltip('close');
                    $el.tooltip('disable');
                }
                this.remove();
            };
            var initComment = function(){
                var $el = $(this) , $widget = $el.tooltip('widget');
                if($widget.is($el)){
                    $el.tooltip({
                        width : 350,
                        maxHeight : 150,
                        hold : 1000,
                        openEvent : 'mouseenter',
                        closeEvent : 'xmouseleave',
                        content : '<ul class="list-comment" />',
                        open: function(){
                            $el.addClass('comment-open');
                            removeTooltip($el.get(0));
                        }
                    });
                    $widget = $el.tooltip('widget').click(function(e){
                        e.preventDefault();
                        e.stopImmediatePropagation();
                    });
                }
                var ds = dataSets[$el.attr('dx')][$el.attr('dy')],$list = $widget.find('ul');
                var type = $el.hasClass('am') ? 'am' : 'pm';
                try{
                    $.each(comments[ds.employee_id][ds.date][type],function(i,v){
                        if(v.user_id == employeeName['id']){
                            var del = $('<a href="javascript:void(0);" class="close" title="'+ t('Delete this comment, you can\'t undo it.') +'">x</a>').click(function(){
                                removeComment.call($(this).parent() ,$el ,i);
                            });
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span> : </h4><div class="comment">%s</div></li>' , t('You'),v.created ,v.text)).append(del));
                        }else{
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span>: </h4><div class="comment">%s</div></li>' , employees[v.user_id] || userComments[v.user_id],v.created,v.text)).append(del));
                        }
                        delete comments[ds.employee_id][ds.date][type][i];
                    });
                }catch(ex){};
                var dx = mapeds['activity'][ds['activity_' +  type]];
                checkActivity(type,ds) && dx && infoActivity.call($widget,$el,type,dx);
                $el.tooltip('enable');
                return $widget;
            };
            var commentHandler = function(data){
                syncHandler.call(this ,{ value : data, url :  updateUrl2} , {} , function($list , data){
                    $list.each(function(){
                        var $el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                        type = $el.hasClass('am') ? 'am' : 'pm';
                        if(data[_ds.employee_id]){
                            var res = data[_ds.employee_id][_ds.date];
                            if(res.result){
                                if(!comments[_ds.employee_id]){
                                    comments[_ds.employee_id] = {};
                                }
                                if(!comments[_ds.employee_id][_ds.date]){
                                    comments[_ds.employee_id][_ds.date] = {};
                                }
                                if(!comments[_ds.employee_id][_ds.date][type]){
                                    comments[_ds.employee_id][_ds.date][type] = {};
                                }
                                comments[_ds.employee_id][_ds.date][type][res['id_'+type]] = {
                                    text : res[type],
                                    employee_id : _ds.employee_id,
                                    user_id : employeeName['id'],
                                    created : res.created
                                };
                                $el.addClass('has-comment');
                                initComment.call($el.get(0));
                            }
                        }
                        $el.removeClass('loading');
                    });
                });
            };
            var removeTooltip = function(self){
                $('#absence-table .comment-open').not(self).each(function(){
                    $(this).removeClass('comment-open').tooltip('close');
                });
            };
            $(document).on("mouseenter", "#absence-table .has-comment", function(e){ 
                var $widget = initComment.call(this);
                if($widget.is(':hidden')){
                    $(this).trigger('mouseenter', e);
                }
            }); 
            $(document).on("mouseleave", "#absence-table .has-comment", function(e){ 
                $(this).tooltip('clear');
            }); 
            $(document).click(function(e){
                removeTooltip($(e.target).closest('td.selectable').get(0));
            });
            
            mapeds['family'] = $.extend( {'-1' : {}, 0 : {name : t('Remove forecast')}}, mapeds['family']);
            if($workloads){
                $.each($workloads, function(key, values){
                    $.each(values, function(_key, _values){
                        $.each(_values, function(k, vl){
                            mapeds['family']['task-'+vl.task_id+'-'+vl.remain_am] = {
                                name: vl.name + ' (' + vl.remain_am + ')'
                            };
                        });
                    });
                });
            }
            (function(){
                //return;
                var initMenuFilter = function($menu){
                    if($menu.prev('.context-menu-filter').length || $menu.children('.context-menu-item').length <= 10){
                        return;
                    }
                    var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                    $menu.before($filter);
                    
                    var timeoutID = null, searchHandler = function(){
                        var val = $(this).val();
                        $menu.children('.context-menu-item').each(function(){
                            var $label = $(this);
                            if(!val.length || $label.text().toLowerCase().indexOf(val.toLowerCase()) != -1){
                                $label.removeClass('notmatch');
                            }else{
                                $label.addClass('notmatch');
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
                var menu = [{}];
                $.each(mapeds['family'] , function(key, data){
                    var rKey = key.substring(0, 4);
                    if(key == '-1'){
                        menu[0][t('Add a comment')] = {
                            onclick : function(imenu, cmenu , e){
                                var $dialog = $('#add-comment-dialog'),self = this;
                                $dialog.dialog({
                                    zIndex : 10000,
                                    modal : true,
                                    close : function(){
                                        $dialog.dialog('destroy');
                                    }
                                });
                                $dialog.find('textarea').val('');
                                $dialog.find('a.ok').unbind().click(function(){
                                    var val = $dialog.find('textarea').val();
                                    if(val){
                                        commentHandler.call(self, val);
                                        $dialog.dialog('close');
                                    }else{
                                        $dialog.find('textarea').focus();
                                    }
                                    return false;
                                });
                                $dialog.find('a.cancel').unbind().click(function(){
                                    $dialog.dialog('close');
                                    return false;
                                });
                            },
                            className: 'add-comment', disabled: false
                        };
                        return;
                    }
                    
                    var opt = {};
                    opt[data.name] = {
                        onclick : function(){
                            if(rKey == 'task'){
                                return absenceHandler.call(this, key , 'task');
                            } else {
                                return absenceHandler.call(this, key , 'family');
                            }
                        },
                        hoverItem: function(c){
                            var offset = $(this).addClass(c).position(),_menu = [];
                            var $menu = $(this).closest('td.context-container'),$sub = $menu.find('.sub-context-' + key);
                            if(!$sub.length){
                                $.each(data.sub || {} , function(undefined,_key){
                                    var _otp = {},_dx = mapeds['subfamily'][_key] || {} , _title = _dx.name || t('No name');
                                    _otp[_title] = {
                                        onclick : function(){
                                            absenceHandler.call(this, _key , 'subfamily');
                                        },
                                        hoverItem : function(){
                                            var offset = $(this).addClass(c).position(),_menu = [];
                                            var $_menu = $(this).closest('td.context-container'),$_sub = $_menu.find('.sub-context-act-' + _key);
                                            if(!$_sub.length){
                                                $.each(_dx.act || {} , function(undefined,_key){
                                                    var _otp = {},_dx = (mapeds['activity'][_key] || {});
                                                    if(!_dx.activated){
                                                        return;
                                                    }
                                                    _otp[_dx.name] = {
                                                        onclick : function(){
                                                            absenceHandler.call(this, _key , 'activity');
                                                        },
                                                        title: _dx.name
                                                    };
                                                    _menu.push(_otp);
                                                });
                                                $_sub = $(contextMenu.createMenu(_menu , contextMenu)).addClass('sub-context sub-context-act sub-context-act-'  + _key).css({
                                                    position: 'absolute', zIndex: 10001
                                                }).hide().addClass(_menu.length ? 'has-sub-context' : 'no-sub-context');
                                                initMenuFilter($_sub.find('td').addClass('context-container').children('.context-menu'));
                                                $_menu.append($_sub);
                                            }
                                            $_menu.find('.sub-context-act').not($_sub).hide();
                                            $_sub.hasClass('has-sub-context') && $_sub.show().css({
                                                top : offset.top + 5,
                                                left : offset.left+ 180
                                            });
                                        },
                                        title: _title
                                    };
                                    _menu.push(_otp);
                                });
                                if(_menu.length && data.act && data.act.length){
                                    _menu.push($.contextMenu.separator);
                                }
                                $.each(data.act || {} , function(undefined,_key){
                                    var _otp = {},_dx = (mapeds['activity'][_key] || {});
                                    if(!_dx.activated){
                                        return;
                                    }
                                    _otp[_dx.name] = {
                                        onclick : function(){
                                            absenceHandler.call(this, _key , 'activity');
                                        },
                                        title: _dx.name
                                    };
                                    _menu.push(_otp);
                                });
                                $sub = $(contextMenu.createMenu(_menu , contextMenu)).addClass('sub-context sub-context-'  + key).css({
                                    position: 'absolute', zIndex: 10000
                                }).hide().addClass(_menu.length ? 'has-sub-context' : 'no-sub-context');
                                initMenuFilter($sub.find('td').addClass('context-container').children('.context-menu'));
                                $menu.append($sub);
                            }
                            $menu.find('.sub-context').not($sub).hide();
                            $sub.hasClass('has-sub-context') && $sub.show().css({
                                top : offset.top + 5,
                                left : offset.left +  ( $(this).closest('table').offset().left + 360 + $sub.width() > $(document).width() ? -180 : 180 )
                            });
                        },
                        className: 'set-' + rKey + ' menu-context' + (key != 0 ? ' menu-item-nonclick' : ''), disabled: false, title: data.name, checkId: rKey
                    };
                    menu.push(opt);
                });
                $container.find('td').bind('click mousedown', function(){
                    var _class = $(this).attr("class") ? $(this).attr("class").split(' ')[0] : '';
                    if(_class == 'wd-fore'){
                        
                        $.each($('.wd-fore'), function(){
                            if($(this).hasClass('fore-selectable')){
                                $(this).removeClass('fore-selectable');
                                $(this).addClass('selectable');
                            }
                            if($(this).hasClass('fore-ui-selectee')){
                                $(this).removeClass('fore-ui-selectee');
                                $(this).addClass('ui-selectee');
                            }
                        });
                        
                        $.each($('.wd-work'), function(){
                            $(this).removeClass('ui-selected');
                            if($(this).hasClass('selectable')){
                                $(this).removeClass('selectable');
                                $(this).addClass('work-selectable');
                            }
                            if($(this).hasClass('ui-selectee')){
                                $(this).removeClass('ui-selectee');
                                $(this).addClass('work-ui-selectee');
                            }
                        });
                    } else {
                        $.each($('.wd-work'), function(){
                            if($(this).hasClass('work-selectable')){
                                $(this).removeClass('work-selectable');
                                $(this).addClass('selectable');
                            }
                            if($(this).hasClass('work-ui-selectee')){
                                $(this).removeClass('work-ui-selectee');
                                $(this).addClass('ui-selectee');
                            }
                        });
                        
                        $.each($('.wd-fore'), function(){
                            $(this).removeClass('ui-selected');
                            if($(this).hasClass('selectable')){
                                $(this).removeClass('selectable');
                                $(this).addClass('fore-selectable');
                            }
                            if($(this).hasClass('ui-selectee')){
                                $(this).removeClass('ui-selectee');
                                $(this).addClass('fore-ui-selectee');
                            }
                        });
                    }
                });
                $container.contextMenu(menu, {shadow : false, theme : 'vista' , beforeShow : function(){
                        contextMenu = this;
                        var checkClass = '';
                        $.each($container.find('td.ui-selected'), function(){
                            var _class = $(this).attr("class") ? $(this).attr("class").split(' ')[0] : '';
                            if(_class == 'wd-fore'){
                                $.each(menu, function(key, values){
                                    $.each(values, function(_key, _values){
                                        if(_values.checkId){
                                            if(_values.checkId == 'task'){
                                                checkClass = $(this).attr("class") ? $(this).attr("class").split(' ')[0] : '';
                                                $('.'+checkClass).addClass('wd-dis-play');
                                            } else {
                                                checkClass = $(this).attr("class") ? $(this).attr("class").split(' ')[0] : '';
                                                $('.'+checkClass).removeClass('wd-dis-play');
                                            }
                                        }
                                    });
                                });
                            } else {
                                $.each(menu, function(key, values){
                                    $.each(values, function(_key, _values){
                                        if(_values.checkId){
                                            if(_values.checkId == 'task'){
                                                checkClass = $(this).attr("class") ? $(this).attr("class").split(' ')[0] : '';
                                                //huupc change removeClass to addClass
                                                //$('.'+checkClass).removeClass('wd-dis-play');
                                                $('.'+checkClass).addClass('wd-dis-play');
                                            } else {
                                                checkClass = $(this).attr("class") ? $(this).attr("class").split(' ')[0] : '';
                                                //huupc change: delete REMOVE FORECASTS in context menu
                                                //if(checkClass != 'set-0'){
//                                                    $('.'+checkClass).addClass('wd-dis-play');
//                                                }
                                                 $('.'+checkClass).addClass('wd-dis-play');
                                            }
                                        }
                                    });
                                });
                            }
                        });
                        initMenuFilter($(contextMenu.menu).find('td').
                            addClass('context-container').children('.context-menu'));
                        if(!$container.find('td.ui-selected').length){
                            return false;
                        }
                        this.menu.width('200');
                    }});
            })();
        });
        
    })(jQuery);
</script>