<?php echo $html->css('context/jquery.contextmenu'); ?>
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
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Absence Requests", true); ?></h2>
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
                        $week = intval(date('W', $_end + DAY + DAY));
                        $year = intval(date('Y', $_end + DAY + DAY));
                        ?>
                        <a id="absence-next" href="<?php echo $this->Html->here . '?week=' . $week . '&year=' . ($week == 1 ? $year + 1 : $year); ?>">
                            <span>Next</span>
                        </a>
                        <?php
                        $week = intval(date('W', $_start - (7 * DAY)));
                        $year = intval(date('Y', $_start - (7 * DAY)));
                        ?>
                        <a id="absence-prev" href="<?php echo $this->Html->here . '?week=' . $week . '&year=' . $year; ?>">
                            <span>Prev</span>
                        </a>
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
                                        <th rowspan="2"><?php __('Capacity'); ?></th>
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
    }
    $_dataView[$day] = $default;
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
    'Holiday' => __('Holiday', true)
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
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="ok"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<script type="text/javascript">
    (function($){
        
        
        $(function(){
        
            var updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'update'))); ?>,
            updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
            deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
            dataSets = <?php echo json_encode($dataView); ?>,
            comments = <?php echo json_encode(@$comments); ?> || {},
            holidays = <?php echo json_encode(@$holidays); ?> || {},
            absences = <?php echo json_encode($absences); ?>,
            employees = <?php echo json_encode($employees); ?>,
            workdays = <?php echo json_encode($workdays); ?>,
            ctClass = <?php echo json_encode($ctClass); ?>,
            employeeName = <?php echo json_encode($employeeName); ?>,
            $container = $('#absence-table').html('');
           
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
            /* --------Custom--------- */
            var absenceHandler = function(save){
                syncHandler.call( this ,{ value : true, url :  updateUrl} , {
                    request : save.id
                } , function($list , data){
                    $list.each(function(){
                        var $ct,ab,$el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                        res = data[_ds.date];
                        if(res && res.result){
                            _ds = $.extend(_ds , data[_ds.date] || {});
                            ab = absences[_ds['absence_' + ($el.hasClass('am') ? 'am' : 'pm')]];
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
                        $el.removeClass('loading');
                    });
                }, function($el){
                    return $el.hasClass('workday') && (save.id != '0' || !$.isNumeric($el.find('span').html()));
                });
            };
            /* --------Draw table--------- */
            $.each(dataSets, function(i){
                var output = '',total = 0;
                $.each(this ,function(day , data){
                    var val = parseFloat(workdays[day]), dt = holidays[data.date] || {},
                    opt = {am : {className : ['am',day] , value : '0'}, pm : {className : ['pm',day] , value : '0'}} ;
                    
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
                            if(comments[data.employee_id][data.date][this]){
                                opt[this].className.push('has-comment');
                            }
                        }catch(ex){};
                        if(data['absence_' +  this]){
                            if(absences[data['absence_' +  this]]){
                                opt[this].value = absences[data['absence_' +  this]].print;
                            }
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
                $container.append('<tr><td class="ct"><span>' + total + '</span></td>' + output + '</tr>');
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
                $el.tooltip('enable');
                return $widget;
            };
            var commentHandler = function(data){
                syncHandler.call(this ,{ value : data, url :  updateUrl2} , {} , function($list , data){
                    $list.each(function(){
                        var $el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                        res = data[_ds.date],type = $el.hasClass('am') ? 'am' : 'pm';
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
                absences = $.extend( {0 : {
                        id : 0,
                        print : t('Remove request')
                    }}, absences);
                $.each(absences , function(undefined, data){
                    var opt = {};
                    opt[data.print] = {
                        onclick : function(imenu, cmenu , e){
                            absenceHandler.call(this, data, imenu , cmenu , e);
                        },
                        disabled: false, title: data.name, className : data.id == 0 ? 'ab-remove' : ''
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
</script>