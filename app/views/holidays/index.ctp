<?php echo $html->css(array('projects')); ?>
<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>

<style type="text/css">
#absence td.holiday span{
    background-color: <?php echo $constraint['holiday']['color']; ?>;
}
#absence td.holiday.repeat span{
    background:<?php echo $constraint['holiday']['color']; ?> url(/img/reset.png) no-repeat right;
}
.wd-input-contries{
    width: auto;
    height: 28px;
    margin-bottom: 15px;
    margin-left: 10px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
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
                                    $avg = intval(($_start + $_end)/2);
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;">
                                    <div id="absence-container" style="min-height:400px;">
                                        <?php
                                        echo $this->element('week');
                                        ?>
                                        <div id="table-control" style="margin-bottom: 0px; margin-left: -10px;">
                                            <?php
                                            echo $this->Form->create('Control', array(
                                                'type' => 'get',
                                                'url' => '/' . Router::normalize($this->here)));
                                            ?>
                                            <fieldset>
                                                <div class="input">
                                                    <?php
                                                    echo $this->Form->year('year', date('Y', $avg) - 5, date('Y', $avg) + 2, date('Y', $avg), array('empty' => false));
                                                    ?>
                                                </div>
                                                <div class="input">
                                                    <?php
                                                    echo $this->Form->month('month', date('m', $avg), array('empty' => false));
                                                    ?>
                                                </div>
                                                <div class="button">
                                                    <input type="submit" value="OK" />
                                                </div>
                                                <?php if($mutil_country): ?>
                                                <select class="wd-input-contries" name="typeRequest" id="typeRequest">
                                                    <?php foreach ($list_country as $id => $name) { ?>
                                                        <option value="<?php echo $id ?>" <?php echo $typeSelect == $id ?'selected' : '';?>><?php echo $name?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php endif; ?>
                                                <!-- <a href="<?php echo $this->Html->url('/holidays/manage/' . date('Y', $avg)) ?>" target="_blank" class="btn-text">
                                                    <img src="<?php echo $this->Html->url('/') ?>img/ui/blank-vision.png" alt="">
                                                    <span><?php __('Synthesis') ?></span>
                                                </a> -->
                                                <div style="clear:both;"></div>
                                            </fieldset>
                                            <?php
                                            echo $this->Form->end();
                                            ?>
                                        </div>
                                        <div id="absence-wrapper" style="margin-left: 0px">
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
            </div>
        </div>
    </div>
</div>


<?php
$dataView = array();
$_dataView = array();
foreach ($dayMaps as $day => $time) {
    $_dataView[$day] = array(
        'employee_id' => '',
        'company_id' => $company_id,
        'date' => $time,
        'am' => isset($holidays[$time]['am']),
        'pm' => isset($holidays[$time]['pm']),
        're_am' => (!empty($holidays[$time]['am']) && $holidays[$time]['am'] == 1) ? 1 : 0,
        're_pm' => (!empty($holidays[$time]['pm']) && $holidays[$time]['pm'] == 1) ? 1 : 0
    );
}
$dataView[] = $_dataView;
$i18ns = array(
    'Add a comment' => __('Add a comment', true),
    'Remove request' => __('Remove request', true),
    'Holiday' => __('Holiday', true),
    'Set to workday' => __('Set to workday', true),
    'Set to holiday' => __('Set to holiday', true),
    'Holiday, Repeat every year.' => __('Holiday, Repeat every year.', true)
);
?>
<div style="display: none;" id="message-template">
    <div class="message error"><?php echo __('Cannot connect to server ...', true); ?><a href="#" class="close">x</a></div>
</div>
<script type="text/javascript">
    (function($){
        $(function(){

            var updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'update', $company_id))); ?>,
            dataSets = <?php echo json_encode($dataView); ?>,
            workdays = <?php echo json_encode($workdays); ?>,
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
                var country_id = <?php echo !empty($country_id) ? json_encode($country_id) : 0; ?>;
                $list.each(function(){
                    var $el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')];
                    if(!_ds || $el.hasClass('loading') || ($.isFunction(check) && check($el) === false)){
                        return;
                    }
                    if(!submit[_ds.date]){
                        submit[_ds.date] = {
                            date: _ds.date,
                            employee_id : _ds.employee_id,
                            country_id : country_id
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
                        var $ct,$el = $(this),_ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                        res = data[_ds.date];
                        if(res && res.result){
                            $el.removeClass('holiday repeat');
                            _ds = $.extend(_ds , data[_ds.date] || {});
                            $ct = $el.parent().find('.ct span');
                            if(save.id != '0'){
                                switch($el.find('span').html()){
                                    case '0.5' :
                                        $ct.html(parseFloat($ct.html()) - 0.5);
                                        break;
                                    default:
                                        $ct.html(parseFloat($ct.html()) + 0.5);
                                        $el.addClass('workday').find('span').html('0.5');
                                }
                                $el.find('span').html(t('Holiday'));
                                $el.addClass('holiday');
                                if(save.id == 2){
                                    $el.addClass('repeat');
                                }
                            }else{
                                $ct.html(parseFloat($ct.html()) + 0.5);
                                $el.addClass('workday').find('span').html('0.5');
                            }
                        }
                        $el.removeClass('loading');
                    });
                }, function($el){
                    return !$el.hasClass('workday') || save.id != '0' || !$.isNumeric($el.find('span').html());
                });
            };
            /* --------Draw table--------- */
            $.each(dataSets, function(i){
                var output = '',total = 0;
                $.each(this ,function(day , data){
                    var val = parseFloat(workdays[day]),
                    opt = {am : {className : ['am',day] , value : '0'}, pm : {className : ['pm',day] , value : '0'}} ;

                    switch(val){
                        case 1:
                            opt['am'].className.push('selectable');
                            opt['pm'].className.push('selectable');
                            break;
                        case 0.5:
                            opt['am'].className.push('selectable');
                    }

                    $.each(['am','pm'] , function(){
                        if(data[this]){
                            opt[this].className.push('holiday');
                            opt[this].value = t('Holiday');
                            if(data['re_' +  this]){
                                opt[this].className.push('repeat');
                            }
                        }else{
                            switch(true){
                                case val == 0.5 && this == 'am' :
                                    total += 0.5;
                                    opt['am'].className.push('workday');
                                    opt['am'].value = 0.5;
                                    break;
                                case val == 1:
                                    total += 0.5;
                                    opt[this].className.push('workday');
                                    opt[this].value = 0.5;
                                    break;
                            }
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
                }
            });
            //return;
            (function(){
                var menu = [];
                $.each([ {id : 0, name : t('Set to workday')},
                    {id : 1, name : t('Set to holiday')},
                    {id : 2, name : t('Holiday, Repeat every year.')}] , function(undefined, data){
                    var opt = {};
                    opt[data.name] = {
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
        $('#typeRequest').change(function(){
            var linkRequest = '/holidays/index/',
                company_id = <?php echo json_encode($company_id) ?>,
                country_id = $(this).val();
            linkRequest = linkRequest + company_id + '/' + country_id;
            window.location.href = linkRequest;
        });
    })(jQuery);
</script>
