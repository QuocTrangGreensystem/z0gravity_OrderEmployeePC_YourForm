<?php echo $html->css(array('context/jquery.contextmenu')); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<style type="text/css">
    .absence-fixed thead tr th{height: 43px;text-align: center;vertical-align: middle;}
    .absence-fixed th,.absence-fixed td.st{
        border-right : 1px solid #87b8d5;
        color: #fff !important;
        text-align: left;
    }
    .absence-fixed .st a{
        color: #fff;
    }
    .absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    #absence-scroll {
        overflow-x: scroll;
    }
    .ch-absen-validation{
        background-color: #F0F0F0;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-wrapper {
        margin: 0 !important;
    }
    #absence-table tr td.ch-absen-validation{background-color: #c3dd8c;}
    #absence-wrapper .absence-fixed{ width: 99% !important;}
    #thColID{ width:8%; } #thColEmployee{ width:70%}
    #absence th.colThDay{min-width:172px;max-width:172px;width:172px;overflow:hidden;}
    .am, .pm{
        overflow:hidden;
        padding-left: 0;
        padding-right: 0;
    }
    .am span, .pm span{
        width:100%;
        word-break:break-all;
    }
    .absence-fixed tbody tr {
      border: 1px solid #ccc;
    }
    .absence-fixed th {
      background: url(../img/front/bg-head-table.png) repeat-x #5fa1c4;
      border-right: 1px solid #87b8d5;
    }
    .absence-fixed thead tr th {
      height: 23px;
      text-align: center;
      vertical-align: middle;
      border: 1px solid #87b8d5;
    }
    .absence-fixed tbody td {
      border-right: 1px solid #ccc;
    }
    .absence-fixed td.st {
      background: url(../img/front/bg-head-table.png) repeat-x #64a3c7;
      border: 1px solid #CACACA;
      color: #fff;
      vertical-align: middle;
      padding-left: 6px;
    }
    .absence-fixed .no{
        text-align: center;
    }
    .rp-waiting span{
        background-color: #E47E0A;
    }
    .absence-fixed tbody td span {
      padding: 3px;
      display: block;
    }
    .absence-fixed tbody td.ct {
      text-align: center;
      background-color: #E8F0FA;
      font-weight: bold;
    }
    .rp-holiday span {
      background-color: #ffff00;
    }
    .absence-fixed td.ui-selected {
      background: none repeat scroll 0 0 #F39814;
      color: white;
    }
    .fixed {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        background: #f0f0f0;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
        margin: 0 !important;
    }
    #menu {
        margin-bottom: 20px !important;
    }
    #table-control td {
        padding-top: 5px;
        vertical-align: middle;
    }
    #auto-cell {
        padding: 2px;
        text-align: center;
    }
</style>
<?php 
$svg_icons = array(
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
		'validated' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="32" height="32" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
		'reject' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32 viewBox="0 0 40 40"><g transform="translate(-323 -71)"><rect class="a" width="32" height="32" transform="translate(-323 -71)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>'
);

?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
             <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
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
                <?php
                    $currentUrl=$_SERVER["REQUEST_URI"];
                    $myParams = $this->params['url'];
                    $del = '';
                    if(isset($myParams['get_path'])){
                        $del .= '&get_path='.$myParams['get_path'];
                    }
                    $currentUrl = str_replace($del,'',$currentUrl);
                    $currentUrl=$currentUrl.'&get_path=1';
                ?>
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <?php
                        echo $this->Form->create('Control', array(
                            'type' => 'get',
                            'id' => 'menu',
                            'url' => '/' . Router::normalize($this->here)));
                        ?>
                            <table id="table-control" class="wd-activity-actions">
                                <tr>
                                    <td id="auto-cell">
                                        <input type="checkbox" id="checkAll" />
                                    </td>
                                    <td>
                                        <fieldset>
                                            <?php
                                            echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false, 'style' => 'padding: 6px'));
                                            echo $this->Form->hidden('month', array('value' => '01'));
                                            ?>
                                            <?php
                                            echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'style' => 'padding: 6px'));
                                            ?>
                                            <a href="<?php echo $currentUrl; ?>" id="expand-pc-btn" class="btn btn-plus"><?php echo $svg_icons['add']; ?></a>
                                            <a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top validate-month" title="<?php __('Validate Requested')?>"><?php echo $svg_icons['validated']; ?></a>
                                            <a href="javascript:void(0)" id="submit-request-no-top" class="validate-for-reject validate-for-reject-top reject-month" title="<?php __('Reject Requested')?>"><?php echo $svg_icons['reject']; ?></a>
                                            <div style="clear:both;"></div>
                                        </fieldset>
                                    </td>
                                </tr>
                            </table>
                        <?php
                        echo $this->Form->end();
                        ?>
                        <div id="absence-wrapper">
                            <?php
                                /**
                                 * Build week
                                 */
                                if(!empty($multiWeeks)):
                                $dateOfYear = round(($_start + $_end)/2, 0);
                                $urlRequests = array('controller' => 'absence_requests', 'action' => 'manage', 'year', 'true', '?' => array('year' => date('Y', $dateOfYear), 'month' => 1, 'profit' => $profit['id'], 'get_path' => $getDataByPath));
                                echo $this->Form->create('Request', array(
                                    'escape' => false, 'id' => 'request-form', 'type' => 'post',
                                    'url' => $urlRequests));

                                    foreach($multiWeeks as $_week => $multiWeek):
                            ?>
                                    <table class="absence-fixed">
                                        <thead>
                                            <tr class="header-height-fixed">
                                                <th rowspan="4" width="30px"><input type="checkbox" id="<?php echo 'week_' . $_week;?>" class="checkList checkAll" /></th>
                                                <th rowspan="4" width="18%"><?php __('Employee'); ?></th>
                                                <th rowspan="4" width="5%"><?php __('Capacity'); ?></th>
                                            </tr>
                                            <?php
                                                $mon = min($multiWeek);
                                                $fri = max($multiWeek);

                                            ?>
                                            <tr><th colspan="10"><?php printf(__('From %s to %s', true), date(' d/m/Y ', $mon), date(' d/m/Y', $fri))?></th></tr>
                                            <tr>
                            <?php
                                        $trMoment = '';
                                        $rowAbsences = $capacityOfEmployFollowWeeks = array();
                                        $totalDayOfWeek = count($multiWeek);
                                        foreach($multiWeek as $dateOfWeek):
                            ?>
                                            <th colspan="2"><?php echo __(date('l', $dateOfWeek));?></th>
                            <?php
                                            $trMoment .= '<th>' . __('AM', true) . '</th><th>' . __('PM', true) . '</th>';
                                            $checkDayHaveData = array();
                                            $_checkHolidays = array_keys($holidays);
                                            if(in_array($dateOfWeek, $_checkHolidays)){
                                                // ngay nghi
                                            } else {
                                                if(!empty($allRequests[$dateOfWeek])){
                                                    foreach($allRequests[$dateOfWeek] as $employ => $datas){
                                                        if (!empty($datas['history'])) {
                                                            $allRequests[$dateOfWeek][$employ]['history'] = unserialize($datas['history']);
                                                        }
                                                        if(!isset($capacityOfEmployFollowWeeks[$employ])){
                                                            $capacityOfEmployFollowWeeks[$employ] = $totalDayOfWeek;
                                                        }
                                                        if(!isset($rowAbsences[$employ])){
                                                            $rowAbsences[$employ] = array();
                                                        }
                                                        if(isset($datas['absence_am']) && !empty($datas['absence_am']) && isset($datas['response_am']) && !empty($datas['response_am']) && $datas['response_am'] === 'waiting'){
                                                            $nameAbsence = !empty($absences[$datas['absence_am']]) && !empty($absences[$datas['absence_am']]['print']) ? $absences[$datas['absence_am']]['print'] : '0.5';
                                                            $class = 'am rp-waiting selectable has-comment';
                                                            $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '" class="' . $class . '"><span>' . $nameAbsence . '</span></td>';
                                                            $capacityOfEmployFollowWeeks[$employ] -= 0.5;
                                                        } else {
                                                            $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '"><span>0.5</span></td>';
                                                        }
                                                        if(isset($datas['absence_pm']) && !empty($datas['absence_pm']) && isset($datas['response_pm']) && !empty($datas['response_pm']) && $datas['response_pm'] === 'waiting'){
                                                            $nameAbsence = !empty($absences[$datas['absence_pm']]) && !empty($absences[$datas['absence_pm']]['print']) ? $absences[$datas['absence_pm']]['print'] : '0.5';
                                                            $class = 'pm rp-waiting selectable has-comment';
                                                            $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '" class="' . $class . '"><span>' . $nameAbsence . '</span></td>';
                                                            $capacityOfEmployFollowWeeks[$employ] -= 0.5;
                                                        } else {
                                                            $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '"><span>0.5</span></td>';
                                                        }
                                                        $checkDayHaveData[$employ] = $employ;
                                                    }
                                                }
                                            }
                                            if(!empty($employOfWeeks[date('W-Y', $dateOfWeek)])){
                                                foreach($employOfWeeks[date('W-Y', $dateOfWeek)] as $employ){
                                                    if(!isset($capacityOfEmployFollowWeeks[$employ])){
                                                        $capacityOfEmployFollowWeeks[$employ] = $totalDayOfWeek;
                                                    }
                                                    if(!empty($holidays[$dateOfWeek])){
                                                        if(isset($holidays[$dateOfWeek]['am'])){
                                                            $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '" class="rp-holiday"><span>' . __('Holiday', true) . '</span></td>';
                                                            $capacityOfEmployFollowWeeks[$employ] -= 0.5;
                                                        }
                                                        if(isset($holidays[$dateOfWeek]['pm'])){
                                                            $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '" class="rp-holiday"><span>' . __('Holiday', true) . '</span></td>';
                                                            $capacityOfEmployFollowWeeks[$employ] -= 0.5;
                                                        }
                                                        continue;
                                                    }
                                                    if(!empty($checkDayHaveData) && !empty($checkDayHaveData[$employ])){
                                                        //da co du lieu ko lam gi them
                                                    } else {
                                                        $rowAbsences[$employ][] = '<td dx="' . $employ . '" dy="' . $dateOfWeek . '" dz="week_' . $_week . '"><span>0.5</span></td><td dx="' . $employ . '" dy="' . $dateOfWeek . '"><span>0.5</span></td>';
                                                    }
                                                }
                                            }
                                        endforeach;
                            ?>
                                            </tr>
                                            <tr><?php echo $trMoment;?></tr>
                                        </thead>
                                            <tbody>
                                            <?php
                                                if(!empty($rowAbsences)):
                                                    foreach($rowAbsences as $em => $da):
                                                        $da = implode(' ', $da);
                                                        $check = '<td class="no checkbox"><input type="checkbox" name="data[id][]" value="' . $em . '-' . $mon . '-' . $fri . '" class="checkAll week_' . $_week . '"></td>';
                                                        $nameEmploy = !empty($employees[$em]) ? $employees[$em] : '';
                                                        $profitE = !empty($profitOfEmployees[$em]) ? $profitOfEmployees[$em] : '';
                                                        $mid = round(($mon+$fri)/2, 0);
                                                        $href = $this->Html->url(array('controller' => 'absence_requests', 'action' => 'index', 'week', '?' => array('id' => $em, 'profit' => $profitE, 'week' => date('W', $mid), 'year' => date('Y', $mid), 'get_path' => $getDataByPath)));
                                                        $resource = '<td class="st"><span><a href="' . $href . '">' . $nameEmploy . '</a></span></td>';
                                                        $capa = !empty($capacityOfEmployFollowWeeks[$em]) ? $capacityOfEmployFollowWeeks[$em] : 0;
                                                        $capacity = '<td class="ct ct_week_' . $_week . '"><span>' . $capa . '</span></td>';
                                                        echo '<tr>' . $check . $resource . $capacity . $da . '</tr>';
                                                    endforeach;
                                                endif;
                                            ?>
                                            </tbody>
                                    </table><br /><br />
                            <?php
                                    endforeach;
                                    echo $this->Form->input('ls', array('name' => 'data[ls]', 'type' => 'hidden', 'value' => time() - 1E9));
                                    echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 0, 'id' => 'ac-validated'));
                                    echo $this->Form->end();
                                endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
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
<div id="dialog-request-all" class="buttons" style="display: none;" title="<?php echo __('Send request for validation', true) ?>">
    <div class="dialog-request-message">

    </div>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="ok"><?php __('OK') ?></a></li>
    </ul>
</div>
<?php
    $i18ns = array(
        'Add a comment' => __('Add a comment', true),
        'Summary' => __('Summary', true),
        'Holiday' => __('Holiday', true),
        'Date requesting' => __('Date requesting', true),
        'Date validate' => __('Date validate', true),
        'Date reject' => __('Date reject', true),
    );
?>
<script>
	var showAllPicture = parseInt(<?php echo json_encode( isset( $companyConfigs['display_picture_all_resource']) ? $companyConfigs['display_picture_all_resource'] : 0); ?>);
    var $container = $('#absence-wrapper'),
    updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'manage_update', '0', $_start, $_end, 'year', true))); ?>,
    updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
    deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
    allRequests = <?php echo json_encode($allRequests); ?>,
    comments = <?php echo json_encode(@$comments); ?> || {},
    employeeName = <?php echo json_encode($employeeName); ?>,
    profitOfEmployees = <?php echo json_encode($profitOfEmployees); ?>,
    constraint = <?php echo json_encode($constraint); ?>;
    var contextMenu = {hide : $.noop};
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
            var $el = $(this),_ds = allRequests[$el.attr('dy')][$el.attr('dx')];
            if(!_ds){
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
        args.url = args.url;
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
            var j = 0;
            var weekRequest = '';
            $list.each(function(){
                var $el = $(this);
                $el.attr('class', '');
                $el.find('span').html('0.5');
                j += 0.5;
                weekRequest = $el.attr('dz');
            });
            var capa = parseFloat($('.ct_'+weekRequest).find('span').html());
            capa += j;
            $('.ct_'+weekRequest).find('span').html(capa);
        }, function($el){
            return $el.hasClass('response') && !$el.hasClass('rp-forecast');
        });
    };
    /**
     * Chon tung cell trong table
     */
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
        var ds = allRequests[$el.attr('dy')][$el.attr('dx')],$list = $widget.find('ul');
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
    /**
     * handle comment
     */
    var commentHandler = function(data){
        syncHandler.call(this ,{ value : data, url :  updateUrl2} , {} , function($list , data){
            $list.each(function(){
                var $el = $(this),_ds = allRequests[$el.attr('dy')][$el.attr('dx')],
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
        $('.absence-fixed .comment-open').not(self).each(function(){
            $(this).removeClass('comment-open').tooltip('close');
        });
    };
    $(document).on("mouseenter", ".absence-fixed .has-comment", function(e){
        var $widget = initComment.call(this);
        if($widget.is(':hidden')){
            $(this).trigger('mouseenter', e);
        }
    });
    $(document).on("mouseleave", ".absence-fixed .has-comment", function(e){
        $(this).tooltip('clear');
    });
    $(document).click(function(e){
        removeTooltip($(e.target).closest('td.selectable').get(0));
    });
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
    $('.checkList').click(function(){
       var getId = $(this).attr('id');
       if($(this).is(':checked')){
            $('.' + getId).prop('checked' , true);
       } else {
            $('.' + getId).prop('checked' , false);
       }
    });
    $('#checkAll').click(function(){
       if($(this).is(':checked')){
            $('.checkAll').prop('checked' , true);
       } else {
            $('.checkAll').prop('checked' , false);
       }
    });
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
        var $form = $('#request-form'),$input = $form.find('.checkbox :checked');
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
        var $form = $('#request-form'),$input = $form.find('.checkbox :checked');
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

    $(window).bind('scroll', function () {
        var menu = $('#menu');
        var table = $('.absence-fixed')
        if ($(window).scrollTop() > 150) {
            menu.addClass('fixed');
            if( table.length )menu.css('padding-left', table.offset().left + 'px');
        } else {
            menu.removeClass('fixed');
            menu.css('padding-left', 0);
        }
    }).bind('resize', function(){
        var t = $('.header-height-fixed th:first');
        if( t.length ){
            $('#auto-cell').width(t.width());
        }
    });
    $(document).ready(function(){
        $(window).trigger('resize');
    });
    $( "#ControlProfit" ).change(function() {
        var currentUrl = updateQueryStringParameter(location.href,'year',$("#yearYear").val());
        currentUrl = updateQueryStringParameter(currentUrl,'profit',$("#ControlProfit").val());

        location.href = currentUrl;
    });
    $( "#yearYear" ).change(function() {
        var currentUrl = updateQueryStringParameter(location.href,'year',$("#yearYear").val());
        currentUrl = updateQueryStringParameter(currentUrl,'profit',$("#ControlProfit").val());
        location.href = currentUrl;
    });
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
</script>
