<?php echo $html->css(array('context/jquery.contextmenu')); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>  
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
<style type="text/css">
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
<?php if($typeSelect!='week'){echo $endDay;}?></style>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
               <div  style="padding-left:20px;">
                <?php /*
                    <select style="border:1px solid #999;padding:2px;" name="typeRequest" id="typeRequest">
                        <option value="week" <?php echo $typeSelect=='week'?'selected':'';?>><?php echo __('Week',true);?></option>
                        <option value="month" <?php echo $typeSelect=='month'?'selected':'';?>><?php echo __('Month',true);?></option>
                        <option value="year" <?php echo $typeSelect=='year'?'selected':'';?>><?php echo __('Year',true);?></option>
                    </select>  
                */ ?>
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
                        <div id="table-control">
                            <?php if($typeSelect=='week'){?>
                            <?php
                            echo $this->Form->create('Control', array(
                                'escape' => false, 'id' => 'request-form', 'type' => 'get',
                                'url' => array('controller' => 'absence_requests', 'action' => 'manage', '?' => array(
                                        'st' => $status, 'profit' => $profit['id'], 'week' => date('W', $_end), 'year' => date('Y', $_end)))));
                            ?>
                            <?php }elseif($typeSelect=='month'){?>
                             <?php
                            echo $this->Form->create('Control', array(
                                'escape' => false, 'id' => 'request-form', 'type' => 'get',
                                'url' => array('controller' => 'absence_requests', 'action' => 'manage','month', '?' => array('month' => date('m', $_end), 'year' => date('Y', $_end),
                                        'st' => $status, 'profit' => $profit['id']))));
                            ?>
                            <?php }else{?>
                            <?php
                            echo $this->Form->create('Control', array(
                                'escape' => false, 'id' => 'request-form', 'type' => 'get',
                                'url' => array('controller' => 'absence_requests', 'action' => 'manage','year', '?' => array('month' => 1, 'year' => date('Y', $_end),
                                        'st' => $status, 'profit' => $profit['id']))));
                            ?>
                            <?php }?>
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
                                    echo $this->Form->month('month', date('m', $_start), array('empty' => false,'type'=>'hidden'));
                                    ?>
                                </div>
                                <div class="input">
                                    <?php
                                    echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false));
                                    ?>
                                </div>
                                <div class="input">
                                    <?php
                                    $params = array_combine(array_keys($constraint), Set::extract('{s}.name', $constraint));
                                    unset($params['forecast'], $params['holiday']);
                                    echo $this->Form->select('st', $params, $status, array(
                                        'empty' => __('--- Status ---', true), 'escape' => false));
                                    ?>
                                </div>
                                <div class="button">
                                    <input type="submit" value="OK" />
                                </div>
                          
                                 <a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top validate-month" title="<?php __('Validate Requested')?>"><span><?php __('Validate Requested'); ?></span></a>
                                <a href="javascript:void(0)" id="submit-request-no-top" class="validate-for-reject validate-for-reject-top reject-month" title="<?php __('Reject Requested')?>"><span><?php __('Reject Requested'); ?></span></a>
                                <div style="clear:both;"></div>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                         <?php if($typeSelect=='week'){?>
                        <?php
                        echo $this->Form->create('Request', array(
                            'escape' => false, 'id' => 'request-form-validation', 'type' => 'post',
                            'url' => array('controller' => 'absence_requests', 'action' => 'manage', '?' => array(
                                    'st' => $status, 'profit' => $profit['id'], 'week' => date('W', $_end), 'year' => date('Y', $_end)))));
                        ?>
                        <?php }elseif($typeSelect=='month'){?>
                         <?php
                        echo $this->Form->create('Request', array(
                            'escape' => false, 'id' => 'request-form-validation', 'type' => 'post',
                            'url' => array('controller' => 'absence_requests', 'action' => 'manage','month', '?' => array('month' => date('m', $_end), 'year' => date('Y', $_end),
                                    'st' => $status, 'profit' => $profit['id']))));
                        ?>
                        <?php }else{?>
                        <?php
                        echo $this->Form->create('Request', array(
                            'escape' => false, 'id' => 'request-form-validation', 'type' => 'post',
                            'url' => array('controller' => 'absence_requests', 'action' => 'manage','year', '?' => array('month' => 1, 'year' => date('Y', $_end),
                                    'st' => $status, 'profit' => $profit['id']))));
                        ?>
                        <?php }?>
                        <div id="absence-wrapper">
                             <?php if($typeSelect!='week'){?>
                                <table id="absence-fixed">
                                    <thead>
                                        <tr>
                                        <th><?php __('#'); ?></th>
                                        <th><?php __('Employee'); ?></th>
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
                                            <th rowspan="2"><?php __('#'); ?></th>
                                            <th rowspan="2"><?php __('Employee'); ?></th>
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

                            <div class="wd-title">
                            <a href="javascript:void(0)" id="submit-request-ok" class="validate-for-validate validate-for-validate-bottom" title="<?php __('Validate Requested')?>"><span><?php __('Validate Requested'); ?></span></a>
                            <a style="margin-left: 53px; margin-top: -32px;" href="javascript:void(0)" id="submit-request-no" class="validate-for-reject validate-for-reject-bottom" title="<?php __('Reject Requested')?>"><span><?php __('Reject Requested'); ?></span></a>
                          </div>
                        <?php
                        echo $this->Form->input('ls', array('name' => 'data[ls]', 'type' => 'hidden', 'value' => time() - 1E9));
                        echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 0, 'id' => 'ac-validated'));
                        echo $this->Form->end();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if($typeSelect=='week'){
$dataView = array();

foreach ($employees as $id => $employee) {
    foreach ($dayMaps as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'response_am' => 0,
            'response_pm' => 0,
            'employee_id' => $id
        );
        if (isset($requests[$id][$time])) {
            unset($requests[$id][$time]['date'], $requests[$id][$time]['employee_id']);
            $default = array_merge($default, array_filter($requests[$id][$time]));
            if (!empty($default['history'])) {
                $default['history'] = unserialize($default['history']);
            }
        }
        if(!empty($workdays[$day]) && $workdays[$day] != 0){
            $dataView[$id][$day] = $default;
        }
    }
}
}else{
     $workdays = $workdaysTmp;
     $dataView = array();
    foreach ($employees as $id => $employee) {
        foreach ($dayMaps as $day => $time) {
            $default = array(
                'date' => $time,
                'absence_am' => 0,
                'absence_pm' => 0,
                'response_am' => 0,
                'response_pm' => 0,
                'employee_id' => $id
            );
            if (isset($requests[$id][$time])) {
                unset($requests[$id][$time]['date'], $requests[$id][$time]['employee_id']);
                $default = array_merge($default, array_filter($requests[$id][$time]));
                if (!empty($default['history'])) {
                    $default['history'] = unserialize($default['history']);
                }
            }
            if(!empty($workdays[$day]) && $workdays[$day] != 0){
                $dataView[$id][$day] = $default;
            }
        }
    }
}
$css = '';
$ctClass = array();
foreach ($constraint as $key => $data) {
    $ctClass[] = "rp-$key";
    $css .= ".rp-$key span {background-color : {$data['color']};}";
}
$ctClass = implode(' ', $ctClass);
$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Summary' => __('Summary', true),
    'Holiday' => __('Holiday', true),
    'Date requesting' => __('Date requesting', true),
    'Date validate' => __('Date validate', true),
    'Date reject' => __('Date reject', true),
);
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
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<div id="dialog-request-all" class="buttons" style="display: none;" title="<?php echo __('Send request for validation', true) ?>">
    <div class="dialog-request-message">

    </div>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"></a></li>
        <li><a href="javascript:void(0)" class="ok"></a></li>
    </ul>
</div>
<script type="text/javascript">
    (function($){
        
        
        $(function(){
            
            
            var openDialog = function(title,callback){
                var $dialog = $('#dialog-request-all').attr('title' , title);
                $dialog.dialog({
                    zIndex : 10000,
                    modal : true,
                    minHeight : 50,
                    close : function(){
                        $dialog.dialog('destroy');
                    }
                });
                $dialog.find('a.ok').unbind().click(function(){
                    if(!$.isFunction(callback)){
                        $dialog.dialog('close');
                    }else{
                        callback.call(this);
                    }
                    return false;
                });
                $dialog.find('a.cancel').unbind().click(function(){
                    $dialog.dialog('close');
                    return false;
                }).toggle($.isFunction(callback));
            };
            $('#submit-request-no, #submit-request-no-top').click(function(){
                var $form = $('#request-form-validation'),$input = $form.find('.checkbox :checked');
                if(!$input.length){
                    openDialog('<?php echo h(__('Reject Absence request?', true)); ?>');
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Please select the employees.', true)); ?>');
                }else{
                    openDialog('<?php echo h(__('Reject Absence request?', true)); ?>',function(){
                        $('#ac-validated').val(0);
                        $form.submit();
                    });
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Are you sure to reject request of selected employees for this week?', true)); ?>');
                }
            });
            $('#submit-request-ok, #submit-request-ok-top').click(function(){
                var $form = $('#request-form-validation'),$input = $form.find('.checkbox :checked');
                if(!$input.length){
                    openDialog('<?php echo h(__('Validate Absence request?', true)); ?>');
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Please select the employees.', true)); ?>');
                }else{
                    openDialog('<?php echo h(__('Validate Absence request?', true)); ?>',function(){
                        $('#ac-validated').val(1);
                        $form.submit();
                    });
                    $('#dialog-request-all .dialog-request-message').html('<?php echo h(__('Are you sure to validate request of selected employees for this week?', true)); ?>');
                }
            });
            
            
            var updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'manage_update', $profit['id'], $_start, $_end))); ?>,
            <?php if($typeSelect=='week'){?>
            requestURL = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'index',$typeSelect, '?' => array('id' => '%2$s', 'profit' => $profit['id'], 'week' => date('W', $_end), 'year' => date('Y', $_end))), array('escape' => false)))); ?>,
            <?php }elseif($typeSelect=='month'){?>
            requestURL = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'index',$typeSelect, '?' => array('id' => '%2$s', 'profit' => $profit['id'], 'month' => date('m', $_end), 'year' => date('Y', $_end))), array('escape' => false)))); ?>,
            <?php }else{?>
            requestURL = <?php echo json_encode(urldecode($this->Html->link('%1$s', array('action' => 'index',$typeSelect, '?' => array('id' => '%2$s', 'profit' => $profit['id'], 'month' => date('m', $_end), 'year' => date('Y', $_end))), array('escape' => false)))); ?>,
            <?php }?>
           
            updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
            deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
            dataSets = <?php echo json_encode($dataView); ?>,
            comments = <?php echo json_encode(@$comments); ?> || {},
            holidays = <?php echo json_encode(@$holidays); ?> || {},
            absences = <?php echo json_encode($absences); ?>,
            constraint = <?php echo json_encode($constraint); ?>,
            workdays = <?php echo json_encode($workdays); ?>,
            employees = <?php echo json_encode($employees); ?>,
            ctClass = <?php echo json_encode($ctClass); ?>,
            employeeName = <?php echo json_encode($employeeName); ?>,
            requestConfirms = <?php echo json_encode($requestConfirms); ?>,
            $container = $('#absence-table').html('');
             <?php if($typeSelect!='week'){?> $containerFixed = $('#absence-table-fixed').html(''); <?php }?>
            /* ------------Sort dataSets ------------------*/
            function sortObj(arr, dataSets){
            	var temp = new Array();
                $.each(arr, function(key, value) {
                    temp.push({v:value, k: key});
                });
                temp.sort(function(a,b){
                   if(a.v > b.v){ return 1}
                    if(a.v < b.v){ return -1}
                      return 0;
                });
                var tmpObj = {};
                $.each(temp, function(key, val){
                    tmpObj['e_'+val.k] = val.v;
                });
                var tmpResults = {};
                $.each(tmpObj, function(key, value){
                    var id = key.replace('e_', '');
                    tmpResults[key] = dataSets[id];
                });
            	return tmpResults;
            }
            var dataSets = sortObj(employees, dataSets);
            /* ------------Sort dataSets End --------------*/
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
                    var $el = $(this),_ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')];
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
            var checkHistory = function(type,dx){
                return !!(dx && (dx['rq_' + type] || dx['rp_' + type]));
            };
            /* --------Custom--------- */
            var absenceHandler = function(data){
                syncHandler.call( this ,{ value : true, url :  updateUrl} , {
                    response : data
                } , function($list , data){
                    $list.each(function(){
                        var $ct,type,$el = $(this),_ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')],
                        type = $el.hasClass('am') ? 'am' : 'pm';
                        if(data[_ds.employee_id]){
                            var res = data[_ds.employee_id][_ds.date];
                            if(res && res.result){
                                _ds = $.extend(_ds , res || {});
                                type = _ds['response_' + type];
                                $el.removeClass(ctClass);
                                $ct = $el.parent().find('.ct span');
                                if(!type || type =='rejetion'){
                                    $ct.html(parseFloat($ct.html()) + 0.5);
                                    $el.removeClass('ui-selected response').addClass('workday').find('span').html('0.5');
                                }
                                $el.addClass('rp-' +  type);
                            }
                            if(res && checkHistory(type,res.history)){
                                $el.addClass('has-comment');
                            }
                        }
                        $el.removeClass('loading');
                    });
                }, function($el){
                    return $el.hasClass('response') && !$el.hasClass('rp-forecast');
                });
            };
            
            /* --------Draw table--------- */
            var dataSum = {total : 0}, sumText = '';sumTextFixed = '';
            var status = {
                0 : t('Requested'),
                2 : t('Validated')
            };
            $.each(dataSets, function(id){
                var id = id.replace('e_', '');
                var output = '', total = 0, select = $.type(requestConfirms[id]) == 'undefined';
                $.each(this ,function(day , data){
                    var dayClass = day.replace(/[^a-zA-Z]/g, '');
                    var val = parseFloat(workdays[day]),dt = holidays[data.date] || {},
                    opt = {am : {className : ['am',dayClass] , value : 0}, pm : {className : ['pm',dayClass] , value : 0}} ;
                    
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
                            if(data['response_' +  this]){
                                opt[this].className.push('response rp-' + data['response_' +  this]);
                            }
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
                            if(data['response_' +  this]){
                                opt[this].className.push('rp-' + data['response_' +  this]);
                            }
                        }
                        if(!dataSum[day+this]){
                            dataSum[day+this] = 0;
                        }
                        if($.isNumeric(opt[this].value)){
                            dataSum[day+this] += opt[this].value;
                        }
                    });
                    $.each(opt, function(){
                        output+= '<td dx="' + id + '" dy="' + day + '" class="' + this.className.join(' ') +'"><span>' + this.value + '</span></td>';
                    });
                });
                dataSum.total += total; 
            <?php if($typeSelect=='week'){?>
                   $container.append('<tr><td class="no checkbox"> ' + (select ? '<input type="checkbox" name="data[id][]" value="' + id + '" /> ' : '') + '</td><td class="st"><span>' + t(requestURL,employees[id] , id) 
                    + (select ? '' : ('<strong> (' + status[requestConfirms[id]] + ')</strong>')) 
                    + '</span><td class="ct"><span>' + total + '</span></td>' + output + '</tr>');
                    });
                    sumText += '<tr class="space"><td colspan="16"><span>&nbsp;</span></td></tr>';
                    sumText += '<tr class="summary"><td class="ct" colspan="2"><span>' + t('Summary') + '</span><td class="ct"><span>' + dataSum.total + '</span></td>';
                    delete dataSum.total;
                    $.each(dataSum , function(){
                        sumText += '<td class=""><span>' + this + '</span></td>';
                    });
                    $container.append(sumText + '</tr>');
               <?php }else{?>
                    $containerFixed.append('<tr><td class="no checkbox"> ' + (select ? '<input type="checkbox" name="data[id][]" value="' + id + '" /> ' : '') + '</td><td class="st"><span>' + t(requestURL,employees[id] , id) 
                    + (select ? '' : ('<strong> (' + status[requestConfirms[id]] + ')</strong>')) 
                    + '</span><td class="ct"><span>' + total + '</span></td></tr>');
                    $container.append('<tr>' + output + '</tr>');
                    });
                    sumText += '<tr class="space"><td colspan="16"><span>&nbsp;</span></td></tr><tr class="summary">';
                    sumTextFixed += '<tr style="height:27px;"></tr><tr class="summary"><td class="ct" colspan="2"><span>' + t('Summary') + '</span><td class="ct"><span>' + dataSum.total + '</span></td>';
                    delete dataSum.total;
                    $.each(dataSum , function(){
                        sumText += '<td class="monday"><span>' + this + '</span></td>';
                    });
                    $container.append(sumText + '</tr>');
                    $containerFixed.append(sumTextFixed + '</tr>');
                <?php }?>
            $('#absence-table .st a').mousedown(function(){
                window.location = $(this).attr('href');
            });
            
            var contextMenu = {hide : $.noop};
            $container.selectable({
                filter : 'td.selectable',
                unselected : function(){
                    contextMenu.hide();
                },
                selected : function(undefined, u){
                    removeTooltip(u.selected);
                }
            });
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
                var ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')],$list = $widget.find('ul');
                var type = $el.hasClass('am') ? 'am' : 'pm';
                try {
                    $.each(comments[ds.employee_id][ds.date][type],function(i,v){
                        if(v.user_id == employeeName['id']){
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
            var commentHandler = function(data){
                syncHandler.call(this ,{ value : data, url :  updateUrl2} , {} , function($list , data){
                    $list.each(function(){
                        var $el = $(this),_ds = dataSets['e_'+$el.attr('dx')][$el.attr('dy')],
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
            /* -------------------------------------- */
            
            (function(){
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
                $.each(constraint , function(key, data){
                    if(key == 'holiday' || key == 'forecast'){
                        return;
                    }
                    var opt = {};
                    opt[data.name] = {
                        onclick : function(imenu, cmenu , e){
                            absenceHandler.call(this, key, imenu , cmenu , e);
                        },
                        className: 'rp-'+key, disabled: false, title: data.name
                    };
                    menu.push(opt);
                });
                $container.contextMenu(menu, {theme : 'vista' , beforeShow : function(){
                        contextMenu = this;
                        if(!$container.find('td.ui-selected').length){
                            return false;
                        }
                        this.menu.width('200');
                    }});
            })();
        });
        
    })(jQuery);
    <?php if(isset($profit['id'])):?>
    <?php $query = isset($profit) ? '&profit=' . $profit['id'] : ''; ?>
    <?php else: ?>
        <?php $query = ""?>
    <?php endif; ?>
    <?php 
        if (!empty($isManage)) {
            $query = '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
        }
        if($this->params['controller'] == 'absence_requests' && isset($this->params['url']['st'])){
            $query .= '&st=' . $this->params['url']['st'];
        }
    ?>
       $('#typeRequest').change(function () {
        if($(this).val()=='week'){
            window.location.href = "/absence_requests/manage/"+ $(this).val()+"?week=<?php echo date('W',$_start);?>&year=<?php echo date('Y',$_start).$query;?>";
        }
        if($(this).val()=='month'){
            window.location.href = "/absence_requests/manage/"+ $(this).val()+"?month=<?php echo date('m',$_start);?>&year=<?php echo date('Y',$_start).$query;?>";
        }
        if($(this).val()=='year'){
             window.location.href = "/absence_requests/manage/"+ $(this).val()+"?month=<?php echo date('m',$_start);?>&year=<?php echo date('Y',$_start).$query;?>";
        }
         
    }); 
</script>