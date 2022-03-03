<?php echo $html->css(array('context/jquery.contextmenu')); ?> 
<?php echo $html->script('context/jquery.contextmenu'); ?> 
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'absence_requests', 'action' => 'export', $employeeName['id'], $employeeName['company_id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <style type="text/css"><?php if($typeSelect!='week'){echo $endDay;}?>.validate-for-validate { background: url(<?php echo $this->Html->webroot('img/sendMail.png'); ?>) no-repeat scroll 0% 0% transparent; margin-left: 10px !important;}
    #absence-fixed thead tr th{height: 43px;text-align: center;vertical-align: middle;}
    #absence-fixed th,#absence-fixed td.st{
        border-right : 1px solid #185790;
        color: #fff !important;
        text-align: left;
    }
    #absence-fixed .st a{
        color: #fff;
    }
    #absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    .wd-title {padding-left: 10px;}
</style>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project" >
                <div  style="padding-left:20px; width: 65px; float: left;">
                <?php /*
                    <select style="border:1px solid #999;padding:2px;" name="typeRequest" id="typeRequest">
                        <option value="week" <?php echo $typeSelect=='week'?'selected':'';?>><?php echo __('Week',true);?></option>
                        <option value="month" <?php echo $typeSelect=='month'?'selected':'';?>><?php echo __('Month',true);?></option>
                        <option value="year" <?php echo $typeSelect=='year'?'selected':'';?>><?php echo __('Year',true);?></option>
                    </select>
                */ ?>  
                </div>
                <div class="wd-title">
                    <?php if (!empty($isManage)) : ?>
                        <h2 class="wd-t1" style="font-size: 1.5em; padding-top: 2px;"><?php echo sprintf(__("%s", true), $employeeName['first_name'] . ' ' . $employeeName['last_name']); ?></h2>  
                    <?php endif; ?>
                </div>
                <div id="message-place">
                    <?php
                    echo $this->Session->flash();
                    $am = __('AM', true);
                    $pm = __('PM', true);
                    $totalColums = 3;
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
                        <div id="table-control" style="clear: both;">
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'id' => 'formControl',
                                'url' => '/' . Router::normalize($this->here)));
                            ?>
                            <fieldset style="margin-left: 22px;">
                              <?php
                                 echo $this->element('week_absence');
                              ?>
                                <div class="input">
                                    <?php
                                    echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false));
                                    ?>
                                </div>
                                <div class="input" <?php if($typeSelect=='year'){ ?> style="display:none;"<?php }?>>
                                    <?php
                                    echo $this->Form->month('month', date('m', $_start), array('empty' => false));
                                    ?>
                                </div>
                                <div class="input">
                                    <?php
                                    //echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false));
                                    ?>
                                </div>
                                <div class="button">
                                    <input type="submit" value="OK" />
                                </div>
                            <?php if (empty($requestMessage) && !$isManage) : ?>
                                 <a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top" title="<?php __('Validate Requested')?>"><span><?php __('Send request message'); ?></span></a>
                            <?php endif; ?>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                        <div id="absence-wrapper">
                            <?php if($typeSelect!='week'){?>
                                <table id="absence-fixed">
                                    <thead>
                                        <tr>
                                            <th><?php __('Capacity'); ?></th>
                                        </tr>     
                                    </thead>
                                    <tbody id="absence-table-fixed">
                                    </tbody>
                                </table>
                            <?php }?>
                            <div id="absence-scroll">
                                <table id="absence">
                                    <thead>
                                        <tr>
                                             <?php if($typeSelect=='week'){?>
                                              <th rowspan="2"><?php __('Capacity'); ?></th>
                                             <?php 
                                                if(!empty($workdays)):
                                                    foreach($workdays as $key => $val):
                                                        if(!empty($val) && $val != 0):
                                                            $totalColums++;
                                            ?>
                                            <th colspan="2" id="<?php echo 'fore'.ucfirst($key);?>"><?php echo __(ucfirst($key)) . __(date(' d ', $dayMaps[$key])) . __(date('M', $dayMaps[$key])); ?></th>
                                            <?php                
                                                        endif;
                                                    endforeach;
                                                endif;
                                            ?>
                                            <?php }else{?>
                                             <?php 
                                                if(!empty($dayWorks)):
                                                    $dayMaps = array();
                                                    $workdaysTmp = array();
                                                    $i=0;
                                                    foreach($dayWorks as $key => $val):
                                                        if($workdays[$val[1]]!=0):
                                                             $totalColums++;
                                                             $keyTmp = $val[1];
                                                             if(!in_array($val[1], $workdays)){ $keyTmp = $val[1].$i;} 
                                                             $workdaysTmp = array_merge($workdaysTmp,array($keyTmp=>1));
                                                              $dayMaps =  array_merge($dayMaps,array($keyTmp => strtotime($val[0].' '.date('Y',$_start))));
                                            ?>
                                            <th colspan="2"><?php echo __(date('l',strtotime($val[0].' '.date('Y', $_start)))).__(date(' d ',strtotime($val[0].' '.date('Y', $_start)))).__(date('M',strtotime($val[0].' '.date('Y', $_start)))); ?></th>
                                            <?php                
                                                        endif;
                                                        $i++;
                                                    endforeach;
                                                endif;
                                            ?>
                                            <?php }?>
                                        </tr>
                                        <tr>
                                        <?php if($typeSelect=='week'){?>
                                            <?php 
                                                if(!empty($workdays)):
                                                    foreach($workdays as $key => $val):
                                                        if(!empty($val) && $val != 0):
                                            ?>
                                            <th><?php echo $am; ?></th>
                                            <th><?php echo $pm; ?></th>
                                           <?php                
                                                        endif;
                                                    endforeach;
                                                endif;
                                            ?>
                                        <?php }else{?>  
                                        <?php 
                                                if(!empty($dayWorks)):
                                                    foreach($dayWorks as $key => $val):
                                                        if($workdays[$val[1]]!=0):
                                            ?>
                                            <th><?php echo $am; ?></th>
                                            <th><?php echo $pm; ?></th>
                                           <?php                
                                                        endif;
                                                    endforeach;
                                                endif;
                                            ?>
                                         <?php }?> 
                                        </tr>
                                    </thead>
                                    <tbody id="absence-table">
                                        <tr><td colspan="15">&nbsp;</td></tr>
                                    </tbody>
                                </table>
                            </div>    
                        </div>
                        <?php if (empty($requestMessage) && !$isManage) : ?>
                            <?php
                            if($typeSelect=='week'){
                               $link = array('controller' => 'absence_requests', 'action' => 'index', '?' => array('week' => date('W', $_end), 'year' => date('Y', $_end))); 
                            }elseif ($typeSelect=='month') {
                                 $link = array('controller' => 'absence_requests', 'action' => 'index','month', '?' => array('month' => date('m', $_end), 'year' => date('Y', $_end))); 
                            }else{
                                $link = array('controller' => 'absence_requests', 'action' => 'index', 'year','?' => array('month' => date('m', $_end), 'year' => date('Y', $_end))); 
                            }
                            echo $this->Form->create('Request', array(
                                'escape' => false, 'id' => 'request-form', 'type' => 'post',
                                'url' => $link));
                            echo $this->Form->hidden('id.' . $employeeName['id'], array('value' => 1));
                            echo $this->Form->end();
                            ?>
                                <div class="wd-title">
                                     <a href="javascript:void(0)" id="submit-request-ok" class="validate-for-validate validate-for-validate-bottom" title="<?php __('Validate Requested')?>"><span><?php __('Send request message'); ?></span></a>
                                </div>
                            <script type="text/javascript">
                                (function($){
                                                                                                                                                                                                                                                                                                                                                                                    
                                    $(function(){
                                        var openDialog = function(callback){
                                            var $dialog = $('#add-comment-dialog2');
                                            $dialog.dialog({
                                                zIndex : 10000,
                                                modal : true,
                                                minHeight : 50,
                                                close : function(){
                                                    $dialog.dialog('destroy');
                                                }
                                            });
                                            $dialog.find('a.ok').unbind().click(function(){
                                                callback.call(this);
                                            });
                                            $dialog.find('a.cancel').unbind().click(function(){
                                                $dialog.dialog('close');
                                                return false;
                                            });
                                        };
                                        $('#submit-request-ok, #submit-request-ok-top').click(function(){
                                            if(!$(this).hasClass('cant-submit')){
                                                return;
                                            }
                                            openDialog(function(){
                                                $('#request-form').submit();
                                            });
                                        });                                                                                                                                                                                                                                                                                                                                            
                                    });
                                                                                                                                                                                                                                                                                                                                                                                    
                                })(jQuery);
                            </script>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if($typeSelect=='week'){
    $dataView = array();
    foreach ($dayMaps as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'response_am' => 0,
            'response_pm' => 0,
            'employee_id' => $employeeName['id']
        );
        if (isset($requests[$time])) {
            unset($requests[$time]['date'], $requests[$time]['employee_id']);
            $default = array_merge($default, array_filter($requests[$time]));
            if (!empty($default['history'])) {
                $default['history'] = unserialize($default['history']);
            }
        }
        if(!empty($workdays[$day]) && $workdays[$day] != 0){
           $_dataView[$day] = $default;
        }
}
}else{
    $workdays = $workdaysTmp;
    $dataView = array();
    foreach ($dayMaps as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'response_am' => 0,
            'response_pm' => 0,
            'employee_id' => $employeeName['id']
        );
        if (isset($requests[$time])) {
            unset($requests[$time]['date'], $requests[$time]['employee_id']);
            $default = array_merge($default, array_filter($requests[$time]));
            if (!empty($default['history'])) {
                $default['history'] = unserialize($default['history']);
            }
        }
        if(!empty($workdays[$day]) && $workdays[$day] != 0){
           $_dataView[$day] = $default;
       }
}
}
$dataView[] = $_dataView;
$css = '';
$ctClass = array();
foreach ($constraint as $key => $data) {
    $ctClass[] = "rp-$key";
    $css .= ".rp-$key span {background-color : {$data['color']};}";
}
$ctClass = implode(' ', $ctClass);
$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Remove request' => __('Remove request', true),
    'Holiday' => __('Holiday', true),
    'Date requesting' => __('Date requesting', true),
    'Date validate' => __('Date validate', true),
    'Date reject' => __('Date reject', true),
);
echo '<style type="text/css">' . $css . '</style>';
$queryUpdate = '?week=' . date('W', $_end) . '&year=' . date('Y', $_end);
if ($isManage) {
    $queryUpdate .= '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
}
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
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<div id="add-comment-dialog2" class="buttons" style="display: none;" title="<?php __('Confirm'); ?>">
    <div class="dialog-request-message">
        <?php __('Confirm? A mail will be sent.'); ?>
    </div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<script type="text/javascript">
    
    (function($){
        
        
        $(function(){
        
            function checkRequest(){
                var $el = $('#submit-request-ok');
                var $elTop = $('#submit-request-ok-top');
                if(!$el.length || !$elTop.length){
                    return;
                }
                if($('#absence .rp-waiting').length){
                    $el.addClass('cant-submit').css('opacity', 1);
                    $elTop.addClass('cant-submit').css('opacity', 1);
                }else{
                    $el.removeClass('cant-submit').css('opacity', 0.5);
                    $elTop.removeClass('cant-submit').css('opacity', 0.5);
                }
            }
            
            var updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'update')) . $queryUpdate); ?>,
            updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
            deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
            requestConfirm = <?php echo json_encode($requestConfirm); ?>,
            dataSets = <?php echo json_encode($dataView); ?>,
            comments = <?php echo json_encode(@$comments); ?> || {},
            holidays = <?php echo json_encode(@$holidays); ?> || {},
            absences = <?php echo json_encode($absences); ?>,
            employees = <?php echo json_encode($employees); ?>,
            workdays = <?php echo json_encode($workdays); ?>,
            ctClass = <?php echo json_encode($ctClass); ?>,
            employeeName = <?php echo json_encode($employeeName); ?>,
            employee_id = <?php echo json_encode($employee_id); ?>,
            $container = $('#absence-table').html('');
            <?php if($typeSelect!='week'){?> $containerFixed = $('#absence-table-fixed').html(''); <?php }?>
            
            
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
                    if(!submit[_ds.date]){
                        submit[_ds.date] = {
                            date: _ds.date,
                            employee_id : _ds.employee_id
                        };
                    }
                    submit[_ds.date][$el.hasClass('am') ? 'am' : 'pm'] = args.value;
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
            var checkHistory = function(type,dx){
                return !!(dx && (dx['rq_' + type] || dx['rv_' + type] || dx['rj_' + type]));
            };
            /* --------Custom--------- */
            var absenceHandler = function(save){
                syncHandler.call( this ,{ value : true, url :  updateUrl} , {
                    request : save.id
                } , function($list , data){
                    $list.each(function(){
                        var $ct,ab,$el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                        res = data[_ds.date], type = $el.hasClass('am') ? 'am' : 'pm';
                        if(res && res.result){
                            _ds = $.extend(_ds , data[_ds.date] || {});
                            ab = absences[_ds['absence_' + type]];
                            $el.removeClass(ctClass);
                            $ct = $el.parent().find('.ct span');
                            if(save.id != '0'){
                                switch($el.find('span').html()){
                                    case '0.5' :
                                        $ct.html(parseFloat($ct.html()) - 0.5);
                                        break;
                                    default:
                                        if(!ab){
                                            $ct.html(parseFloat($ct.html()) + 0.5);
                                            $el.addClass('workday').find('span').html('0.5');
                                        }
                                }
                                if(ab){
                                    $el.find('span').html(ab.print);
                                    if(ab.id == '-1'){
                                        $el.addClass('rp-forecast');
                                    }else{
                                        $el.addClass('rp-waiting');
                                    }
                                }
                            }else{
                                $ct.html(parseFloat($ct.html()) + 0.5);
                                $el.addClass('workday').find('span').html('0.5');
                            }
                        }
                        if(res && checkHistory(type,res.history)){
                            $el.addClass('has-comment');
                        }else if(save.id == 0){
                            var $widget = $el.tooltip('widget').find('ul.list-comment');
                            if($widget.length){
                                $widget.find('li.info').remove();
                                if($widget.children().length == 0){
                                    $el.removeClass('has-comment');
                                    $el.tooltip('close');
                                    $el.tooltip('disable');
                                }
                            }
                        }
                        $el.removeClass('loading');
                    });
                    checkRequest();
                }, function($el){
                    return $el.hasClass('workday') && (save.id != '0' || !$.isNumeric($el.find('span').html()));
                });
            };
            /* --------Draw table--------- */
            $.each(dataSets, function(i){
                var output = '',total = 0;
                $.each(this ,function(day , data){
                    var dayClass = day.replace(/[^a-zA-Z]/g, '');
                    var val = parseFloat(workdays[day]), dt = holidays[data.date] || {},
                    opt = {am : {className : ['am',dayClass] , value : '0'}, pm : {className : ['pm',dayClass] , value : '0'}} ;
                    
                    switch(val){ 
                        case 1:
                            if(!dt['am']){
                                opt['am'].className.push('selectable');
                            }else{
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                            if(!dt['pm']){
                                opt['pm'].className.push('selectable');
                            }else{
                                opt['pm'].className.push('rp-holiday');
                                opt['pm'].value = t('Holiday');
                            }
                            break;
                        case 0.5:
                            if(!dt['am']){
                                opt['am'].className.push('selectable');
                            }else{
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                    }
                    
                    $.each(['am','pm'] , function(){
                        try {
                            if(checkHistory(this,data.history) || comments[data.employee_id][data.date][this]){
                                opt[this].className.push('has-comment');
                            }
                        }catch(ex){};
                        if(data['absence_' +  this]){
                            opt[this].value = (absences[data['absence_' +  this]] || {}).print || t('Hidden');
                            opt[this].className.push(data['absence_' +  this]);
                            if(data['response_' +  this] != 'validated' || !data['response_' +  this]){
                                opt[this].className.push('workday');
                            }
                        }else{
                            val = parseFloat(workdays[day]);
                            switch(true){ 
                                case val == 0.5 && this == 'am' && !dt['am'] :
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
                        if(data['response_' +  this]){
                            opt[this].className.push('rp-' + data['response_' +  this]);
                        }
                    });
                    $.each(opt, function(){ 
                        output+= '<td dx="' + i + '" dy="' + day + '" class="' + this.className.join(' ') +'"><span>' + this.value + '</span></td>';
                    });
                });
                <?php if($typeSelect!='week'){?>
                    $container.append('<tr>' + output + '</tr>');
                    $containerFixed.append('<tr><td class="ct"><span>' + total + '</span></td></tr>');
               <?php }else{?>
                    $container.append('<tr><td class="ct"><span>' + total + '</span></td>' + output + '</tr>');
                <?php }?>
            });
            var contextMenu = {hide : $.noop};
            //$container.selectable({
//                filter : 'td.selectable',
//                unselected : function(){
//                    contextMenu.hide();
//                },
//                selected : function(undefined, u){
//                    removeTooltip(u.selected);
//                }
//            });
            var absenceHistory = function($el,type,data){
                var $list = $(this).find('ul.list-comment');
                var $info = $list.find('.info-comment').html('');
                if(!$info.length){
                    $info = $('<div class="comment info-comment"></div>');
                    var $del = $('<a class="close" title="'+ t('Close') +'">x</a>').click(function(){
                        $(this).closest('li').hide();
                        if($list.children().length == 1){
                            $.each(data , function(i){
                                delete data[i];
                            });
                            $el.removeClass('has-comment');
                            $el.tooltip('close');
                            $el.tooltip('disable');
                        }
                        return false;
                    });
                    $list.prepend($(t('<li class="info"><h4 class="title">%s</h4></li>' , t('Absence information'))).append($del).append($info));
                }
                if(data['rq_' + type]){
                    $info.append('<span><strong>' + t('Date requesting') + '</strong>: ' + String(data['rq_' + type]) +  '</span>');
                }
                if(data['rv_' + type]){
                    $info.append('<span><strong>' + t('Date validate') + '</strong>: ' + String(data['rv_' + type]) +  '</span>');
                }
                if(data['rj_' + type]){
                    $info.append('<span><strong>' + t('Date reject') + '</strong>: ' + String(data['rj_' + type]) +  '</span>');
                }
            };
            /* --------Comment--------- */
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
                        width : 300,
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
                try {
                    $.each(comments[ds.employee_id][ds.date][type],function(i,v){
                        if(v.user_id == employee_id){
                            var del = $('<a href="javascript:void(0);" class="close" title="'+ t('Delete this comment, you can\'t undo it.') +'">x</a>').click(function(){
                                removeComment.call($(this).parent() ,$el ,i);
                            });
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span> : </h4><div class="comment">%s</div></li>' , t('You'),v.created ,v.text)).append(del));
                        }else{
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span>: </h4><div class="comment">%s</div></li>' , employees[v.user_id],v.created,v.text)).append(del));
                        }
                        delete comments[ds.employee_id][ds.date][type][i];
                    });
                }catch(ex){};
                $el.tooltip('enable');
                checkHistory(type,ds.history) && absenceHistory.call($widget,$el,type,ds.history);
                return $widget;
            };
            var syncHandler2 = function(args , dsubmit , callback , check){
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
            var commentHandler = function(data){
                syncHandler2.call(this ,{ value : data, url :  updateUrl2} , {} , function($list , data){
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
                                    user_id : employee_id,
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
            /* -------------------------------------- */
            checkRequest();
            (function(){
                /*--------------------------- HuuPC add new -------------------------*/
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
                /*----------------------------End -----------------------------------*/
                var menu = [{}];
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
                if(!requestConfirm){
                    function Absences() {
                        var href = '';
                        if((window.location.pathname == '/absence_requests/')||(window.location.pathname == '/absence_requests/index/week')||(window.location.pathname == '/absence_requests/index/month')||(window.location.pathname == '/absence_requests/index/year')) {
                            var dateCurrent = new Date();
                            var _month = dateCurrent.getMonth()+1;
                            href = "/absence_requests/requestApi?year=" +dateCurrent.getFullYear()+ "&month=" +_month+ "#";
                        } else {
                            $.urlParam = function(name){
                                var results = new RegExp('[\\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
                                return results || 0;
                            }
                            _href = window.location.search;
                            _href = _href.substr(1, 4);
                            if(_href == 'year'){
                                href = "/absence_requests/requestApi?year=" +$.urlParam('year')+ "&month=" +$.urlParam('month')+ "#"; 
                            } else {
                                href = "/absence_requests/requestApi?week=" +$.urlParam('week')+ "&year=" +$.urlParam('year')+ "#";
                            }
                        }
                        var result = "";
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
                    var _absences = Absences();
                    _absences = $.extend( {0 : {
                            id : 0,
                            print : t('Remove request')
                        }}, _absences);
                    $.each(_absences , function(undefined, data){
                        if(Number(this.id) > 0 && !Number(this.activated)){
                            return;
                        }
                        var opt = {};
                        var _data = data.print;
                        var _rq = (data.request) ? data.request : 0;
                        var _vl = (data.total) ? data.total : '';
                        var _view;
                        if(!_vl){
                            if(!_rq){
                                _view = '';
                            } else {
                                 _view = '  (' + _rq + '/' + 'NA' +')';
                            }
                        } else {
                            if(!_rq){
                                _view = '  (' + 0 + '/' + _vl +')';
                            } else {
                                 _view = '  (' + _rq + '/' + _vl +')';
                            }
                        }
                        _data = _data + _view;
                        opt[_data] = {
                            onclick : function(imenu, cmenu , e){
                                if($(imenu).attr("id") == 'wd-disabled'){
                                    return false;
                                }
                                absenceHandler.call(this, data, imenu , cmenu , e);
                            },
                            disabled: false, title: data.name, className : data.id == 0 ? 'ab-remove' : 'wd-list'+data.id
                        };
                        menu.push(opt);
                    });
                }
                $container.contextMenu(menu, {theme : 'vista' , beforeShow : function(){
                        contextMenu = this;
                        //total select date
                        var dataSend = [];
                        var _count = $container.find('td.ui-selected').length;
                        $.each($container.find('td.ui-selected'), function(index, value){
                            var _dx = $(this).attr('dx');
                            var _dy = $(this).attr('dy');
                            var _timeDay = $(this).attr('class').split(' ')[0];
                            var _time = dataSets[_dx][_dy].date ? dataSets[_dx][_dy].date : 0;
                            dataSend[index] = _time;
                        });
                        //count cell request
                        _count = (_count) ? _count * 0.5 : 0;
                        //ajax request absences
                        function Absences() {
                            var href = '';
                            if((window.location.pathname == '/absence_requests/')||(window.location.pathname == '/absence_requests/index/week')||(window.location.pathname == '/absence_requests/index/month')||(window.location.pathname == '/absence_requests/index/year')) {
                                var dateCurrent = new Date();
                                var _month = dateCurrent.getMonth()+1;
                                href = "/absence_requests/requestApi?year=" +dateCurrent.getFullYear()+ "&month=" +_month+ "#";
                            } else {
                                $.urlParam = function(name){
                                    var results = new RegExp('[\\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
                                    return results[1] || 0;
                                }
                                _href = window.location.search;
                                _href = _href.substr(1, 4);
                                if(_href == 'year'){
                                    href = "/absence_requests/requestApi?year=" +$.urlParam('year')+ "&month=" +$.urlParam('month')+ "#"; 
                                } else {
                                    href = "/absence_requests/requestApi?week=" +$.urlParam('week')+ "&year=" +$.urlParam('year')+ "#";
                                }
                            }
                            var result = "";
                            $.ajax({
                              url: href,
                              async: false, 
                              dataType: 'json',
                              data: {
                                dateCurrent: dataSend
                              },
                              success:function(data) {
                                 result = data; 
                              }
                           });
                           return result;
                        }
                        var _absences = Absences();
                        $.each(_absences , function(undefined, data){
                            var _rq = data.request;
                            var _total = data.total;
                            _rq = (_rq) ? _rq + _count : _count;
                            var _data = data.print;
                            var _rqView = data.request;
                            var _view = '';
                            //if(data.request){
                                if(!_total){
                                    if(!_rqView){
                                        _view = '';
                                        _view = _data + _view;
                                        $('.wd-list'+data.id).find('.context-menu-item-inner').text(_view);
                                    } else {
                                         _view = '  (' + _rqView + '/' + 'NA' +')';
                                         _view = _data + _view;
                                         $('.wd-list'+data.id).find('.context-menu-item-inner').text(_view);
                                    }
                                } else {
                                    if(!_rqView){
                                        _view = '  (' + 0 + '/' + _total +')';
                                        _view = _data + _view;
                                        $('.wd-list'+data.id).find('.context-menu-item-inner').text(_view);
                                    } else {
                                         _view = '  (' + _rqView + '/' + _total +')';
                                         _view = _data + _view;
                                         $('.wd-list'+data.id).find('.context-menu-item-inner').text(_view);
                                    }
                                }
                            //}
                            $(contextMenu.menu).find('.wd-list'+data.id).hover(
                                function () {
                                    if(_total){
                                        if(_rq > _total){
                                            $(this).attr('id', 'wd-disabled');
                                            $(this).addClass('wd-bt-no');   
                                        } else {
                                            $(this).addClass('wd-bt-yes');
                                            $(this).removeAttr('id');
                                            $(this).removeClass('wd-bt-no');
                                        }
                                    } else {
                                        $(this).addClass('wd-bt-yes');
                                        $(this).removeAttr('id');
                                        $(this).removeClass('wd-bt-no');
                                    }
                                    
                                },
                                function () {
                                    $(this).removeAttr('id');
                                    $(this).removeClass('wd-bt-no');
                                    $(this).removeClass('wd-bt-yes');
                                }
                            );
                        });
                        initMenuFilter($(contextMenu.menu).find('td').addClass('context-container').children('.context-menu'));
                        if(!$container.find('td.ui-selected').length){
                            return false;
                        }
                        this.menu.width('200');
                    }});
            })();
        });
    
    })(jQuery); 
    $('#typeRequest').change(function () {
        if($(this).val()=='week'){
            window.location.href = "/absence_requests/index/"+ $(this).val()+"?week=<?php echo date('W',$_start);?>&year=<?php echo date('Y',$_start);?>";
        }
        if($(this).val()=='month'){
            window.location.href = "/absence_requests/index/"+ $(this).val()+"?month=<?php echo date('m',$_start);?>&year=<?php echo date('Y',$_start);?>";
        }
        if($(this).val()=='year'){
             window.location.href = "/absence_requests/index/"+ $(this).val()+"?month=<?php echo date('m',$_start);?>&year=<?php echo date('Y',$_start);?>";
        }  
    });    
</script>