<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<style type="text/css">
	.wd-tab .wd-panel{
		    padding: 25px 20px;
			border: none;
	}
	#table-control{
		margin-bottom: 20px;
	}
    #absence-fixed thead tr th{text-align: center;vertical-align: middle;}
    #absence-fixed th,#absence-fixed td.st{
        border-right : 1px solid #5fa1c4;
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
    #absence-scroll {
        overflow-x: scroll;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-wrapper{
        width: 100%;
    }
    #absence-container{
        margin-left: -20px;
    }
    #absence-wrapper #absence-fixed{ width: 12% !important;}
    #thColID{ width:8%; } #thColEmployee{ width:70%;}
    #absence th.colThDay{
        /*min-width: 37px;
        max-width: 37px;
        width: 37px;*/
        overflow:hidden;
    }
    #absence-table-fixed td.available a{
        color: #635d5d;
        font-weight: normal;
        font-size: 11px;
    }
    .fixedHeight, .fixedHeight td{
        line-height: 14px;
    }
    .am, .pm{
        width: 25px;
        min-width: 25px;
        max-width: 25px;
        overflow:hidden;
        padding-left: 0;
        padding-right: 0;
    }
    .am span, .pm span{
        width:100%;
        word-break:break-all;
    }
    #profit{
        width: 267px;
    }
    .ms-parent{
        width: 265px !important;
    }
    .btn.btn-favory{
        width: 40px;
        height: 40px;
        text-align: center;
        line-height: 40px;
        font-weight: normal !important;
		border: 1px solid #E1E6E8;
		box-sizing: border-box;
		
    }
    .btn.btn-favory:hover{
        text-decoration: none;
    }
	#table-control form .btn{
		width: 32px;
        height: 32px;
		line-height: 32px;
		box-sizing: border-box;
		padding: 0;
	}
	#table-control form .btn:hover i{
		color: #217FC2;
	}
	#table-control form select{
		height: 32px;
		line-height: 32px;
		border: 1px solid #E1E6E8;
		border-radius: 3px;
		-webkit-appearance: none;
		-moz-appearance: none;
		-ms-appearance: none;
		-o-appearance: none;
		appearance: none;
		background: url(/img/new-icon/down.png) no-repeat right 10px center #fff !important;
		padding: 0px 5px;
		min-width: 100px;
	}
	#table-control form select,
	#table-control form .btn, #filter_profitCenter, #filter_employee{
		margin-right: 6px;
	}
	
    .btn-favory:before{
       content: "\e09b";
       font-size: 20px;
       color: #424242;
       font-family: 'simple-line-icons';
    }
    .text-textarea{
        margin-top: 10px;
        margin-left: 185px;
        width: 200px;
        height: 26px;
        padding-left: 5px;
    }
    .input label{
        bottom: 10px;
        position: absolute;
        display: none;
    }
    .validate-for-validate{
        border: none;
        margin-top: 10px;
        left: 400px;
    }
    .validate-for-validate:hover{
        cursor: pointer;
    }
    .favory-content{
        min-height: 50px;
        min-width: 480px;
        border-bottom: 1px solid #67a5c9;
        max-height: 400px;
        overflow-x: hidden;
        overflow-y: auto;
        margin-top: 25px;
    }
    .wd-left{
        min-width: 50px;
        max-width: 200px;
    }
    .error{
        background-color: #ffcece;
        background-position: 15px -1005px;
        border-color: #ec7e8b;
        text-align: center;
        width: 200px;
        margin-left: 300px;
        color: #c00;
        padding: 5px;
        border-radius: 3px;
    }
    .success{
        background-color: #d5ffce;
        background-position: 15px -1505px;
        border-color: #82dc68;
        text-align: center;
        width: 200px;
        margin-left: 300px;
        color: #c00;
        padding: 5px;
        border-radius: 3px;
    }
    .message{
        background: #dbe3ff url(/img/front/message.gif) no-repeat 15px -6px;
        border: #8195d6 solid 1px;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        margin: 5px auto;
        padding: 8px 15px 8px 45px;
        position: relative;
        zoom: 1;
        width: 500px;
        color: #c00;
    }
    table.display td{
        vertical-align: middle;
    }
    table.display th{
        vertical-align: middle;
    }
    .wd-action{
        min-width: 60px;
    }
    .rp-validated span{
        width: 100%;
        height: 100%;
        background-color: #c1ff06 !important;
    }
    .rp-holiday span{
        width: 100%;
        height: 100%;
        background-color: #ffff00;
    }
    .saturday_am, .saturday_pm, .sunday_am, .sunday_pm{
        background-color: #eee !important;
    }
    tbody#absence-table tr td{
        height: 34px;
    }
    #absence tbody td, #absence-task tbody td{
        padding: 0px;
    }
    a.available-title{
        color: #000;
        font-size: 14px;
    }
    .month-name h3{
        text-align: center;
        font-size: 20px;
        color: orange;
    }
    tbody#absence-table-fixed tr td{
        height: 30px;
        vertical-align: middle;
    }
    table tbody tr:hover td{
        background-color: transparent;
    }
    #absence-fixed tbody td span{
        padding: 0px;
    }
    tr{
        vertical-align: middle;
    }
    #absence tbody td span, #absence-task tbody td span{
        padding: 0px;
    }
    .favory-name-hidden{
        display: none;
    }
</style>
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
                <div class="wd-table" id="project_container" style="width:100%;">
                    <div id="absence-container" style="min-height:400px;">
                        <div id="table-control" class="wd-activity-actions" style="overflow: visible; min-width:1024px;">
                            <?php
                            echo $this->Form->create('Control', array(
                                'type' => 'get',
                                'url' => '/' . Router::normalize($this->here)));
                            $PCModel = ClassRegistry::init('ProfitCenter');
                            $menuListProfitCenters = $PCModel->generateTreeList(array('company_id' => $employee_info['Company']['id']),null,null,' -- ',-1);
                            ?>
                            <fieldset style="margin-left: 22px; clear: both">
                                <?php echo $this->Form->month('month', date('m', $_start), array('empty' => false)) ?>
                                <?php echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false)) ?>
                                <div class="" id="filter_profitCenter" style="overflow: visible; display: inline-block">
                                    <?php
                                    echo $this->Form->input('profit_center', array(
                                        'type' => 'select',
                                        'name' => 'pro',
                                        'id' => 'profit',
                                        'div' => false,
                                        'multiple' => true,
                                        'hiddenField' => false,
                                        'label' => false,
                                        'rel' => 'no-history',
                                        "empty" => false,
                                        "options" => !empty($menuListProfitCenters) ? $menuListProfitCenters : array(),
                                    ));
                                    ?>
                                </div>
                                <div class="" id="filter_employee" style="overflow: visible; display: inline-block">
                                    <?php
                                    echo $this->Form->input('employee', array(
                                        'type' => 'select',
                                        'name' => 'emp',
                                        'id' => 'availEmployee',
                                        'div' => false,
                                        'multiple' => true,
                                        'hiddenField' => false,
                                        'label' => false,
                                        'rel' => 'no-history',
                                        "empty" => false,
                                        "options" => !empty($listEmployees) ? $listEmployees : array(),
                                    ));
                                    ?>
                                </div>
                                <button class="btn btn-go"></button>
                                <a href="javascript:void(0)" class="btn btn-reset" id="reset_filter"><i class="icon-refresh"></i></a>
                                <a href="javascript:void(0)" class="btn btn-favory" id="favory"><i class="icon-star"></i></a>
                            </fieldset>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                        <div class="month-name"><h3><?php echo __(date('F', $_start), true) . ' ' . date('Y', $_start); ?></h3></div>
                        <div id="absence-wrapper">
                            <div id="scrollTopAbsence" class="useLeftScroll"><div id="scrollTopAbsenceContent"></div></div>
                            <br clear="all"  />
                            <div id="scrollLeftAbsence">
                                <div id="scrollLeftAbsenceContent"></div>
                            </div>
                            <table id="absence-fixed">
                            <tr class="elmTemp">
                            <td class="elmTemp">
                            <table>
                                <thead>
                                    <tr class="header-height-fixed">
                                        <th id="thColEmployee"><?php __('Employee'); ?></th>
                                    </tr>
                                </thead>
                             </table>
                             </td>
                             </tr>
                             <tr class="elmTemp">
                                <td class="elmTemp">
                                    <div class="tbl-tbody" >
                                    <table>
                                        <tbody id="absence-table-fixed"></tbody>
                                    </table>
                                    </div>
                                </td>
                             </tr>
                            </table>
                            <div id="absence-scroll">
                                <table id="absence">
                                <tr class="elmTemp">
                                <td class="elmTemp">
                                <table>
                                    <thead>
                                        <tr class="header-height">
                                        <?php
                                            $trTop = '';
                                            if(!empty($listWorkingDays)){
                                                $j=0;
                                                $sDate = $_start;
                                                while($sDate <= $_end){
                                                    $j++;
                                                    $_top = substr(__(date('l', $sDate), true), 0, 1) . __(date(' d', $sDate), true);
                                                    $trTop .= '<th class="colThDay" colspan="2">' . $_top . '</th>';
                                                    $sDate = mktime(0, 0, 0, date("m", $sDate), date("d", $sDate)+1, date("Y", $sDate));
                                                }
                                            }
                                        ?>
                                        <?php echo $trTop;?>
                                        </tr>
                                    </thead>
                                </table>
                                </td></tr>
                                <tr class="elmTemp"><td class="elmTemp">
                                <div class="tbl-tbody" >
                                <table >
                                <tbody id="absence-table">
                                </tbody>
                                </table>
                                </div>
                                </td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div></div></div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_detail_value') ?>
<div id="dialog-favory" style="display: none">
    <h3 style="text-align: center;" id="message"  class=""></h3>
    <div class="favory-content">
        <table class="display">
            <thead>
                <tr class="wd-header">
                    <th class="wd-left"></th>
                    <th class="wd-left"><?php echo __("Team(s)", true) ?></th>
                    <th class="wd-left"><?php echo __("Resource(s)", true) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($favory as $value) {
                    $dx = $value['FavoryAbsence'];
                    $_profitName = explode(', ', $dx['profit_id']);
                    $_p = '';
                    foreach ($_profitName as $val) {
                        if(empty($_p)){
                            $_p .= (!empty($profit[$val]) ? $profit[$val] : ' ');
                        } else {
                            $_p .= ', '.(!empty($profit[$val]) ? $profit[$val] : ' ');
                        }
                    }
                    $_empName = explode(', ', $dx['emp_id']);
                    $_e = '';
                    foreach ($_empName as $val) {
                        if(empty($_e)){
                            $_e .= (!empty($listEmployees[$val]) ? $listEmployees[$val] : ' ');
                        } else {
                            $_e .= ', ' . (!empty($listEmployees[$val]) ? $listEmployees[$val] : ' ');
                        }
                    }
                    $link = '/absence_requests/available?month='. $month.'&year=' .$year. $dx['url'];
                    echo '<tr class="favory-'.$dx['id'].'">';
                    echo '<td class="favory-name" style="width: 100px;"><a target="_blank" class="available-title" href="'.$link.'">'. $dx['title'] .'</a></td>';
                    echo '<td style="width: 200px;">'. $_p .'</td>';
                    echo '<td style="width: 300px;">'. $_e .'</td>';
                    echo '<td class="wd-action"><a href="'.$link.'" class="wd-edit"></a><div class="wd-bt-big"><a href="javascript:void(0)" onclick="deleteFavory('.$dx['id'].')" class="wd-hover-advance-tooltip"></a></div></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <div>
        <span style="margin-right: -170px; margin-left: 15px; font-size: 15px; line-height: 18px;"><?php echo __('New list'); ?></span>
        <input class="text-textarea" type="text" value='' />
        <button class="validate-for-validate validate-for-validate-top" id="save-favory"></button>
    </div>
</div>
<?php
$dataView = array();
foreach ($employees as $id => $employee) {
    $sDate = $_start;
    while($sDate <= $_end){
        $default = array(
            'date' => $sDate,
            'absence_am' => 0,
            'absence_pm' => 0,
            'response_am' => 0,
            'response_pm' => 0,
            'employee_id' => $id
        );
        if (isset($requests[$id][$sDate])) {
            unset($requests[$id][$sDate]['date'], $requests[$id][$sDate]['employee_id']);
            $default = array_merge($default, array_filter($requests[$id][$sDate]));
            if (!empty($default['history'])) {
                $default['history'] = unserialize($default['history']);
            }
        }
        $dataView[$id][$sDate] = $default;
        $sDate = mktime(0, 0, 0, date("m", $sDate), date("d", $sDate)+1, date("Y", $sDate));
    }
}
// debug($dataView); exit;
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
    'Deleted' => __('Deleted', true),
    'Saved' => __('Saved', true),
    'This list already exist' => __('This list already exist', true)
);
// echo '<style type="text/css">' . $css . '</style>';
?>
<script type="text/javascript">
    var dataSets = <?php echo json_encode($dataView); ?>;
    var employees = <?php echo json_encode($employees) ?>;
    var dayHasValidations = <?php echo json_encode($dayHasValidations); ?>;
    var _url = <?php echo json_encode($this->params['url']); ?>,
        workdays = <?php echo json_encode($workdays); ?>,
        holidays = <?php echo json_encode(@$holidays); ?> || {},
        $containerFixed = $('#absence-table-fixed').html(''),
        $container = $('#absence-table').html(''),
        absences = <?php echo json_encode($absences); ?>,
        _profit = <?php echo json_encode($_profit); ?>,
        _emp = <?php echo json_encode($_emp); ?>,
        month = <?php echo json_encode($month); ?>,
        year = <?php echo json_encode($year); ?>,
        profit = <?php echo json_encode($profit); ?>,
        profitOfEmployees = <?php echo json_encode($profitOfEmployees) ?>,
        daysInWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    var _emps = $.map(_emp, function(value) {
        return [value];
    });
    $.z0.History.load('vs_filter', function(data){
        $('#profit').multipleSelect('setSelects', _profit);
        $('#availEmployee').multipleSelect('setSelects', _emps);
    });
    $("#reset_filter").on('click', function(){
        //RESET
        $('#profit').multipleSelect('setSelects', []);
        $('#availEmployee').multipleSelect('setSelects', []);
        return false;
    });
    var multiPc= $('#profit').multipleSelect({
        minimumCountSelected: 0,
        position: 'bottom',
        placeholder: '<?php __("-- Any --") ?>'
    });
    var multiResource = $('#availEmployee').multipleSelect({
        minimumCountSelected: 0,
        position: 'bottom',
        placeholder: '<?php __("-- Any --") ?>'
    });
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
    /* --------Check day has activity request--------- */
    var checkHasAcRequest = function(day, listDays){
        var result = false;
        if(listDays){
            $.each(listDays, function(ind, val){
                if(parseInt(day) === parseInt(val)){
                    result = true;
                    return result;
                }
            });
        }
        return result;
    };
    var objectLength =function (obj) {

        var result = 0;
        for(var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
            // or Object.prototype.hasOwnProperty.call(obj, prop)
                result++;
            }
        }
        return result;
    };
    var objectToArray =function (obj) {

        var result = [];
        for(var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
            // or Object.prototype.hasOwnProperty.call(obj, prop)
                result.push(prop);
            }
        }
        return result;
    };
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

    var status = {
        0 : t('Requested'),
        2 : t('Validated')
    };
    $('#favory').on('click', function(){
        var _html = $('#dialog-favory');
        $('#dialog-favory').show();
        jQuery('#dialogDetailValue').css({'min-width':700, 'min-height': 130, 'max-height': 530});
        jQuery('#contentDialog').html(_html);
        showMe();
        $('.text-textarea').select().focus();
    });
    $('#save-favory').on('click', function(){
        var name = $('.text-textarea').val();
        var check = false;
        $(".favory-name").each(function() {
            var a = $(this).find('a').html();
            if( name.toLowerCase() == a.toLowerCase()){
                var _ht = t("This list already exist");
                $('#message').html(_ht);
                $('#message').addClass('error');
                $('#message').show();
                setTimeout(function(){
                    $('#message').hide();
                },4000);
                check = true;
            }
        });
        if(name == '' || check === true){
            return false;
        }
        $.ajax({
            url: '<?php echo $html->url('/absence_requests/saveFavory/') ?>',
            cache : true,
            type: 'post',
            dataType: 'json',
            data: {
                name: name,
                url: _url,
                profit: _profit,
                emp: _emp
            },
            success: function(data){
                if(data){
                    var _html = '';
                    var link = '/absence_requests/available?month='+month+'&year=' + year + data['url'];
                    _html += '<tr>';
                    _html += '<td class="favory-name" style="width: 100px;"><a target="_blank" class="available-title" href="'+link+'" class="wd-edit">' + data['title'] + '</a></td>';
                    _html += '<td style="width: 300px;">' + data['profit'] + '</td>';
                    _html += '<td style="width: 300px;">' + data['employee'] + '</td>';
                    _html += '<td class="wd-action"><a href="'+link+'" class="wd-edit"></a><div class="wd-bt-big"><a href="javascript:void(0)" onclick="deleteFavory('+data['id']+')" class="wd-hover-advance-tooltip"></a></div></td>';
                    _html += '</tr>';
                    $('.display tr:last').after(_html);
                    $('#message').show();
                    var _ht = t("Saved");
                    $('#message').html(_ht);
                    $('#message').addClass('success');
                    setTimeout(function(){
                        $('#message').hide();
                    },4000);
                }
            }
        });
    });
    function deleteFavory(_id){
        if (!confirm(t("Deleted"))){
            return false;
        }
        $.ajax({
            url: '<?php echo $html->url('/absence_requests/deleteFavory/') ?>',
            cache : true,
            type: 'post',
            data: {
                id: _id
            },
            success: function(data){
                if(data){
                    $('#message').show();
                    $('.favory-'+_id).addClass('favory-name-hidden');
                    $('.favory-'+_id).find('.favory-name').addClass('favory-name-hidden');
                    $('.favory-'+_id).find('.favory-name').removeClass('favory-name');
                    var _ht = t("Deleted");
                    $('#message').html(_ht);
                    $('#message').addClass('success');
                    setTimeout(function(){
                        $('#message').hide();
                    },4000);
                }
            }
        });
    }
    //------
    $.each(dataSets, function(id){

        var id = id.replace('e_', '');
        var output = '', total = 0;
        var select = true;
        var _dayHasValidations = dayHasValidations[id] ? dayHasValidations[id] : [];
        var j =0 ;
        $.each(this ,function(day , data){
            j++;
            var _day = day * 1000;
            _day = new Date(_day);
            _day = daysInWeek[_day.getDay()];
            var val = parseFloat(workdays[_day]),dt = holidays[data.date] || {},
            opt = {am : {className : ['am',day] , value : ''}, pm : {className : ['pm',day] , value : ''}} ;
            switch(val){
                case 1:
                    if(!dt['am']){
                        //select && opt['am'].className.push('selectable');
                        if(checkHasAcRequest(day, _dayHasValidations)){
                            opt['am'].className.push('ch-absen-validation');
                        } else {
                            opt['am'].className.push('selectable');
                        }
                    }else{
                        opt['am'].className.push('rp-holiday');
                        opt['am'].value = t('Holiday');
                    }
                    if(!dt['pm']){
                        //select && opt['pm'].className.push('selectable');
                        if(checkHasAcRequest(day, _dayHasValidations)){
                            opt['pm'].className.push('ch-absen-validation');
                        } else {
                            opt['pm'].className.push('selectable');
                        }
                    }else{
                        opt['pm'].className.push('rp-holiday');
                        opt['pm'].value = t('Holiday');
                    }
                    break;
                case 0.5:
                    if(!dt['am']){
                        //select && opt['am'].className.push('selectable');
                        if(checkHasAcRequest(day, _dayHasValidations)){
                            opt['am'].className.push('ch-absen-validation');
                        } else {
                            opt['am'].className.push('selectable');
                        }
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
                            opt['am'].className.push('workday');
                            break;
                        case val == 1 && !dt[this]:
                            total += 0.5;
                            opt[this].className.push('workday');
                            break;
                    }
                    if(data['response_' +  this]){
                        opt[this].className.push('rp-' + data['response_' +  this]);
                    }
                }
                opt[this].className.push(_day + '_' + this);
            });
            $.each(opt, function(){
                output+= '<td dx="' + id + '" dy="' + day + '" class="' + this.className[0]+j+' '+ this.className.join(' ') +'"><span></span></td>';
            });
        });
        $containerFixed.append('<tr class="fixedHeight height_' +id+ '"><td class="available"><span><a target="_blank" href="/absence_requests/index/month?id='+id+'&amp;profit='+profitOfEmployees[id]+'&year='+year+'&month='+month+'&get_path=0">' +
        employees[id] + ' - ' + profit[profitOfEmployees[id]]
            + '</a></span></tr>');
        $container.append('<tr class="fixedHeight height_' +id+ '">' + output + '</tr>');
    });
    $container.append('</tr>');
    $(window).resize(function(e) {
        configSizeScroll();
    });
    var original = $('.header-height').height();
    var gw = 15;
    function configSizeScroll(hd){
        $("#scrollTopAbsenceContent").width($("#absence").width());
        $("#scrollTopAbsence").width($("#absence-scroll").width());
        $("#scrollLeftAbsenceContent").height($("#absence-table").height());
        var hHead=original;
        $("#scrollLeftAbsence").css({'marginTop':(hHead)+'px'});
        if(hd!=600)
        {
            hd=hd-hHead-25;
        }
        $("#scrollLeftAbsence").height(hd);
    }
    setTimeout(function(){
        $(window).resize();
        configSizeScroll(600);
    }, 100);
    var allowScrollWindow = true;
    var abc = 0;
    $(document).on('onmousewheel wheel onmousewheel mousewheel DOMMouseScroll', function(event, delta) {
        if(event.originalEvent.wheelDelta) {
            delta = event.originalEvent.wheelDelta;
        } else {
            delta = event.originalEvent.deltaY * -1;
        }
        if(allowScrollWindow == false) {
            if(delta < 0) {
                abc = abc == $("#absence-table").height() ? $("#absence-table").height() : abc + 120;
            } else {
                abc = abc == 0 ? abc : abc - 120;
            }
            $('#scrollLeftAbsence').animate({scrollTop:abc},'fast');
            return false;
        }
    });
    $("#scrollTopAbsence").scroll(function () {
        $("#absence-scroll").scrollLeft($("#scrollTopAbsence").scrollLeft());
    });
    $("#absence-scroll").scroll(function () {
        $("#scrollTopAbsence").scrollLeft($("#absence-scroll").scrollLeft());
    });
    $("#scrollLeftAbsence").scroll(function (e) {
        $(".tbl-tbody").scrollTop($('#scrollLeftAbsence').scrollTop());
        if(allowScrollWindow == true)
        abc = $('#scrollLeftAbsence').scrollTop();
        $("#absence-table-fixed").scrollTop($('#scrollLeftAbsence').scrollTop());
    });
    $("#absence-scroll").mouseover(function(e) {
        allowScrollWindow = false;
    });
    $("#absence-scroll").mouseout(function(e) {
        allowScrollWindow = true;
    });
    $("#absence-fixed").mouseover(function(e) {
        allowScrollWindow = false;
    });
    $("#absence-fixed").mouseout(function(e) {
        allowScrollWindow = true;
    });
	
	//
	$('.project-task-widget').on('click', '.status_dots .status_dot', function(){
        if(read_only) return;
		var _this = $(this);
		if( _this.hasClass('active') || _this.hasClass('loading') || _this.closest('.status_dots').hasClass('loading') ) return;
		_this.closest('.status_dots').addClass('loading');
		_this.addClass('loading').siblings().removeClass('loading');
		_task_id = _this.data('taskid');
		_status_id = _this.data('value');
		$.ajax({
			type: "POST",
            url: '/kanban/update_task_status',
            data: {
					id: _task_id,
					status: _status_id
			},
            dataType: 'json',
            success: function(respon){
				_this.closest('.status_dots').removeClass('loading');
				var _texts = _this.closest('.task-status');
				_texts.find('.status_text').removeClass('active');
				_texts.find('.status_text[data-value="' + _status_id + '"]').addClass('active');
				_this.removeClass('loading');
				if( respon.result == true){
					_this.addClass('active').siblings().removeClass('active');
					_this.addClass('puffIn'); 
					task_item = '<div class="jqx-rc-all" id="kanban_'+_task_id+'">'+ $('.jqx-kanban-column').find('#kanban_'+_task_id).html() +'</div>';
					$('.jqx-kanban-column').find('#kanban_'+_task_id).remove();
					 $('[data-kanban-column-container="'+_status_id+'"]').append(task_item);
					var wait = window.setTimeout( function(){
						_this.removeClass('puffIn');
					}, 1500 );
				}else{
					_this.removeClass('active').siblings().removeClass('active');
				}
            },
			error: function(){
				_this.closest('.status_dots').removeClass('loading');
				_this.removeClass('loading');
				_this.removeClass('active').siblings().removeClass('active');
			}
		});
	});
</script>
