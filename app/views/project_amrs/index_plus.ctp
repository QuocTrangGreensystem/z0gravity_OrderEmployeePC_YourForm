<?php
echo $html->script('jshashtable-2.1');
echo $html->script('jquery.numberformatter-1.2.3');
echo $html->script('jquery.formatCurrency-1.4.0');
echo $html->script('jquery.validation.min');
echo $html->css('jquery.multiSelect');
echo $html->script('validateDate');
echo $html->css('dd');
echo $html->script('jquery.dd');
echo $html->css('gantt');
echo $this->Html->script(array(
    'dashboard/jqx-all',
    'dashboard/jqxchart_preview',
    'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider',
    'chart/highcharts.js',
    'chart/exporting.js',
    'html2canvas',
    'jquery.html2canvas.organization',
    'jquery.scrollTo',
    'autosize.min'
));
echo $this->Html->css(array(
    'dashboard/jqx.base',
    'dashboard/jqx.web'
));
$EPM_see_the_budget = isset($companyConfigs['EPM_see_the_budget']) && !empty($companyConfigs['EPM_see_the_budget']) ?  true : false;
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
?>
<?php
echo $this->Form->create('Export', array('url' => array('controller' => 'project_amrs', 'action' => 'export_pdf'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));

echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
//echo $this->Form->hidden('rows', array('value' => //$rows));
echo $this->Form->end();
?>
<style>
    body{color: #000;}
    .error-message{color:red;margin-left:35px;}.wd-weather-list ul li{padding-right:5px;}#GanttChartDIV{width:86%;clear:both;margin-left:165px;}.gantt-chart-wrapper{overflow-x:auto;}.gantt-chart{margin-left:0!important;}#gantt-display div label{padding:0 6px;}#gantt-display{width:620px;}
    .inputcheckbox{float:left;width:50px;margin-left:-20px;margin-top:1px;}
    /*.group-content{border-top:1px solid #004381;margin-bottom:15px; overflow: hidden;}
    .group-content h3{float:left;padding-top:10px;background:url("<?php echo $this->Html->webroot('img/line-text2.png'); ?>");width:165px;height:26px;text-align:center;}
    .group-content h3 span{color:#FFF;font-weight:400;font-size:13px;}
    .group-content fieldset{float:left;margin-left:5px;}
    .group-content fieldset div.wd-submit{padding:2px 0!important;}
    .group-content fieldset div.wd-submit a.wd-reset{margin-left:5px!important;}*/
    .wd-t2-plus{margin-bottom:5px;font-size:20px; color: orange}
    .selection-plus{float:left;border:1px solid #d4d4d4;margin-left:10px;padding:6px;}
    fieldset div.wd-input{width:90%!important;}
    #table-cost table{width:80%;}#table-cost table tr td{border:1px solid #d4d4d4;text-align:center;padding:5px;}#table-cost table tr td.cost-header{background-color:#64a3c7;color:#FFF;}#table-cost table tr td.cost-md{background-color:#75923C;color:#FFF;}#table-cost table tr td.cost-euro{background-color:#95B3D7;color:#FFF;}.cost-disabled{background-color:#F5F5F5;}.checkbox,.wd-weather-list ul li input,.wd-weather-list ul li img,.wd-weather-list-dd ul li input,.wd-weather-list-dd ul li img{float:left;} .highcharts-container{ border:1px solid #999 !important;}.budget_external_chart{ margin-bottom:30px;}
    .delay-plan{text-align: center;font-weight: bold;color:#000;margin-top: -25px;font-size: 13px; padding-bottom: 9px;}
    .gantt-wrapper{float: left;width: 86%;}
    #GanttChartDIV{width: 100%;float: left;margin-left: 0;}
    .wd-weather-list-dd ul li{width: 80px;}.demo-gauge{float: left; margin-left:165px; width:240px !important; margin-top: 10px;}.num-progress{ text-align:center; margin-top:-20px; margin-left: 48px;}
    .gantt-msi{
        position: absolute;
    }
    .gantt-msi i{
        background: url("/img/mi.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi-blue i{
        background: url("/img/mi-blue.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi-green i{
        background: url("/img/mi-green.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi-orange i{
        background: url("/img/mi-orange.png") no-repeat center;
        cursor:pointer;
        height: 16px;
        width: 16px;
        text-indent: -999px;
        overflow: hidden;
        margin-left: -8px;
        float: left;
    }
    .gantt-msi span, .gantt-msi-blue span, .gantt-msi-green span, .gantt-msi-orange span{
        float: left;
        white-space: nowrap;
    }
    .pch_log_system {
        overflow: hidden;
        border-bottom: 1px solid #999;
    }
    .pch_log_description textarea {
        width: 99%;
        height: 99%;
        border: 0;
        font-size: 13px;
        background-color: #fff;
        border: 0;
        border-right: 1px solid #e0e0e0;
        overflow: auto;
    }
    .pch_log {
        min-height: 100px;
        border: 1px solid #E0E0E0;
        border-top: 0;
        position: relative;
    }
    .pch_log_system_content {
        margin-top: 33px;
        height: 111px !important;
        overflow-y: scroll;
    }
    .pch_log_avatar_content {
        background: #fff;
        border: none !important;
    }
    .wd-title a.wd-add-project {
        padding-left: 26px;
    }

    .kpi-widget {
        clear: both;
        overflow: hidden;
    }
    .kpi-visible-0 {
        display: none;
    }
    .progress-pie__bg {
        fill: rgba(255, 255, 255, 0.5);
    }

    .progress-pie__text {
        fill: #00426b;
        font-family: "Iceland", sans-serif;
        letter-spacing: -2; }

    .progress-pie__gradient stop {
        stop-color: #00fff7; }

    .progress-pie__gradient stop + stop {
        stop-color: #00426b; }

    .progress-pie__inner-disc {
        fill: white;
    }
    .sg-section--progress-pie .progress-pie {
        width: 18em;
        height: 18em;
    }
    .export-pdf-icon-all{
        background: url("/img_z0g/export-pdf.png") no-repeat !important;
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: top;
        opacity: 1;
    }
    #overlay-container{
        display: none;
    }
    #overlay-wrapper{
        position: fixed;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 10000;
        background: #000;
        opacity: 0.5;
        filter:alpha(opacity=50);
    }
    #overlay-box{
        position: fixed;
        top: 50%;
        left: 50%;
        z-index: 10001;
        height: 100px;
        margin-left: -100px;
        margin-top: -50px;
        padding-top: 70px;
        width: 200px;
        text-align: center;
        font-weight: bold;
        background: url("../img/loader.gif") top center no-repeat;
    }
    .group-content > h3{
        /*background: none;*/
        background-color: #64a3c7;
    }
    #wd-container-footer{
        display: none;
    }
    .group-content .progress-circle{
        width: 270px;
    }
	#svgChart circle{
		position: relative;
		stroke-width: 15px;
		-ms-stroke-width: 15px;
		stroke-opacity: 0.3;
		-ms-stroke-opacity: 0.3;
		transition: all 0.4s ease;
		-ms-transition: all 0.4s ease;
	}

	.jqx-rc-all.jqx-button{
		box-shadow: 0 5px 10px 1px rgba(29,29,27,0.06);
	}
	.jqx-rc-all.jqx-button  span span{
		border-left: 10px solid transparent;
		border-right: 10px solid transparent;
		border-top: 10px solid #fff;
		position: absolute;
		bottom: -10px;
		left: 50%;
		transform: translateX(-50%);
		display: block;
		
	}
	.jqx-rc-all.jqx-button  .jqx-chart-tooltip-text{
		color: inherit;
	}
	.budget-progress .progress-label span + span {
		height: 9px;	width: 40px;	color: #666666;	font-family: "Open Sans";	font-size: 12px;	line-height: 20px;
	}
	.wd-tab {
		max-width: 1920px;
	}
</style>
<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
?>
<div id="wd-container-main" class="wd-project-detail">
    <div id="chart-wrapper" class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> <div class="wd-panel">
            <h2 class="wd-t2-plus" style="float: left;"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']) ?></h2>
            <?php
            echo $this->Form->create('ProjectAmr', array('url' => array(
                    'controller' => 'project_amrs', 'action' => 'index_plus', $projectName['Project']['id']
                    )));
            echo $this->Form->input('project_id', array('type' => 'hidden', 'name' => 'data[ProjectAmr][project_id]', 'value' => $project_id));
            echo $this->Form->input('id', array('type' => 'hidden', 'name' => 'data[ProjectAmr][id]', 'value' => (@$this->data['ProjectAmr']['id']) ? $this->data['ProjectAmr']['id'] : ""));
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            ?>
            <fieldset style="float: left; margin-top: -20px; margin-left: 15px;">
                <div class="wd-submit export-hidden" style="display: none">
                    <button type="submit" class="btn btn-save" id="btnSave">
                        <span><?php __('Save') ?></span>
                    </button>
                    <a href="" class="btn btn-reset-red"></a>
                    <a href="#" onclick="SubmitDataExport();return false;" class="export-pdf-icon-all" title="<?php __('Export PDF')?>"><span></span></a>
                </div>
            </fieldset>
            <div class="wd-input wd-weather-list" style="float: left;">
                <ul style="float: left; display: inline; margin-top: 2px; margin-left: 300px">
                    <!--li style="width: 50px;padding-top: 10px;"><?php //__('Weather'); ?></li-->
                    <li><input class="input_weather" checked="true" <?php echo !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.svg') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.svg') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.svg') ?>"  /></li>
                    <li style="margin-left: 90px;"><input class="input_weather" checked="true" <?php echo !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["rank"] == 'up' ? 'checked' : 'checked'; ?> value="up" name="data[ProjectAmr][rank][]" type="radio" /> <img title="Up"  src="<?php echo $html->url('/img/up.svg') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["rank"] == 'down' ? 'checked' : ''; ?> value="down" name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;" /> <img title="Down" src="<?php echo $html->url('/img/down.svg') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["rank"] == 'mid' ? 'checked' : ''; ?> value="mid" name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;"   /> <img title="Mid"  src="<?php echo $html->url('/img/mid.svg');?>"/></li>
                </ul>
            </div>
            <?php if($employee_info['Role']['name'] == 'admin'): ?>
            <a style="margin-left: 30px" href="<?php echo $this->Html->url('/kpi_settings/index/ajax') ?>" class="button-setting"></a>
            <?php endif; ?>
            <?php echo $this->element("checkStaffingBuilding") ?>
            <p style="clear: both; color: rgb(255, 2, 2); font-size: 11px; font-weight: bold;">
                <?php
                    if( !isset($this->data['Project']['last_modified']) || !($time = $this->data['Project']['last_modified']) ){
                        $time = $projectName['Project']['updated'];
                    }
                    $updated = $time ? date('H:i:s A d/m/Y', $time) : '../../....';
                    $byEmployee = !empty($projectName['Project']['update_by_employee']) ? $projectName['Project']['update_by_employee'] : 'N/A';
                    echo __('Last Update: ', true) . $updated . __(' by ', true) . $byEmployee;
                ?>
            </p>
            <?php echo $this->Session->flash(); ?>
            <div style="clear: both; margin-bottom: 20px"></div>
<?php
//tat ca widget nam trong views/elements/widgets/
//mobile version se co dang: mkpi-ten_widget
/*
=======Cach them moi widget========
1. kpi_settings_controller
    ::get
        $default
            ten_widget|01
            //0 = hide
            //1 = show
2. tao file widget
Tat ca code css, js cua widget nen cho vao file widget luon, ko nen de o day
*/
$orders = $this->requestAction('/kpi_settings/get');
foreach($orders as $f){
    list($field, $visible) = explode('|', $f);
    if($resetRole == 3 && $field == 'budget' && ((!$EPM_see_the_budget && !$seeBudgetPM))){
        continue;
    }
    $file = 'widgets/kpi-' . $field;
    if( file_exists(ELEMENTS . DS . $file . '.ctp') ){
        echo '<div class="kpi-widget kpi-visible-' . $visible . '" data-widget="kpi-'. $field .'">';
        echo $this->element($file, array(
            'type' => $type
        ));
        echo '</div>';
    }
}
?>
</div></div>

        </div>
</div>

</div>
<?php
echo $this->element('dialog_projects');
echo $validation->bind("ProjectAmr");
echo $html->script('jquery.ba-bbq.min');
echo $html->script('jquery.multiSelect');
?>

<style type="text/css">
    .setvalidation{
        border-color: red !important;
    }
</style>
<script language="javascript">
    var myavatar = <?php echo $this->UserFile->avatarjs() ?>.replace('{id}', <?php echo $employee_info['Employee']['id'] ?>);
    var projectName = <?php echo json_encode($projectName['Project']); ?>;
    var countDataSet = <?php echo json_encode($countDataSet); ?>;
    $('#budget_db').width(countDataSet * 50);
    $(document).ready(function () {

        var years    = <?php echo json_encode($setYear); ?>,
        manDays    = <?php echo json_encode($manDays); ?>,
        dataSets    = <?php echo !empty($dataSets) ? json_encode($dataSets) : json_encode(array()); ?>;
        var settings = {
                title: "",
                description: years,
                padding: { left: 5, top: 0, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
                source: dataSets,
                categoryAxis:
                    {
                        dataField: 'date',
						description: '',
						showGridLines: true,
						color: "#DDDDDD",
                    },
                colorScheme: 'scheme02',
                seriesGroups:
                    [
                        {
                            type: 'splinearea',
                            showLabels: false,//default
                            valueAxis:
                            {
                                axisSize: 'auto',
                                minValue: 0,
                                maxValue: manDays,
                                unitInterval: manDays,
                                description: '',
                                displayValueAxis: false
                            },
                            series: [
                                    // { dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                                    { dataField: 'consumed', lineWidth: 4, displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 0}, color: '#538FFA'},
									{ dataField: 'validated', lineWidth: 4, displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: 0}, color: '#E44353'}

                                ]
                        },
                    ]
            };


            // dash board budget external
            <?php
            foreach($dataExternals as $_external=> $_dataExternal)
            { ?>
                var years    = <?php echo json_encode($_dataExternal['setYearExternal']); ?>,
                manDays    = <?php echo json_encode($_dataExternal['manDayExternals']); ?>,
                dataSets    = <?php echo json_encode($_dataExternal['dataSetsExternal']); ?>;
                var settingsExternal = {
                        title: "<?php echo $_dataExternal['setProviderName']; echo " : "; echo $_dataExternal['setNameExternal']; echo " "; echo __($md, true) . ' ' . __('Planed Follow Up', true);?>",
                        description: years,
                        padding: { left: 5, top: 5, right: 5, bottom: 5 },
                        titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                        source: dataSets,
                        categoryAxis:
                            {
                                dataField: 'date',
                                description: 'Time',
                                showGridLines: false
                            },
                        colorScheme: 'scheme02',
                        seriesGroups:
                            [
                                {
                                    type: 'spline',
                                    showLabels: false,//default
                                    valueAxis:
                                    {
                                        axisSize: 'auto',
                                        minValue: 0,
                                        maxValue: manDays,
                                        unitInterval: manDays/10,
                                        description: '',
                                        displayValueAxis: false
                                    },
                                    series: [
                                            //{ dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                                            { dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 10}, color: '#f05a24'},
                                            { dataField: 'planed', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: -10}, color: '#0cb0e0'}

                                        ]
                                },
                                {
                                    type: 'spline',
                                    showLabels: true,
                                    showLegend: false,
                                    columnsGapPercent: 100,
                                    showGridLines: false,
                                    valueAxis: {
                                        minValue: 0,
                                        maxValue: manDays,
                                        unitInterval: manDays/10,
                                        description: '',
                                        displayValueAxis: false

                                    },
                                    series: [
                                                {
                                                    dataFieldFrom: 'planed',
                                                    dataFieldTo: 'consumed',
                                                    //displayText: 'Different between Consumed and Validated',
                                                    formatFunction: caculate,
                                                    color: '#FF0000',
                                                    labelOffset: {x: 20, y: 0}
                                                }
                                            ]

                                }
                            ]
                    };
                    $('#budget_external_<?php echo $_external; ?>').jqxChart(settingsExternal);
                <?php } ?>

            function caculate(value){
               value = value.from - value.to;
               return  Math.round(value * 100) / 100 ;

            }
            $('#budget_db').jqxChart(settings);


    });

    // function onNext() {
    //     var elmnt = document.getElementById("budget-inner");
    //     elmnt.scrollLeft += 50;
    // }

    // function onPrevous() {
    //     var elmnt = document.getElementById("budget-inner");
    //     elmnt.scrollLeft -= 50;
    // }
    function removeLine(checkboxObject,type){
        if(checkboxObject.checked){
            if(type=="n"){
                $('.gantt-line-n').show();
                $('.gantt-line-desc').show();
            };
            if(type=="s"){
                 $('.gantt-line-s').show();
                 $('.gantt-line-desc').show();
            };
        }else{
            if(type=="n"){
                if(!$('#displayreal').attr("checked"))
                    $('.gantt-line-desc').hide();
                $('.gantt-line-n').hide();
            }
            if(type=="s"){
                if(!$('#displayplan').attr("checked"))
                    $('.gantt-line-desc').hide();
                $('.gantt-line-s').hide();
            }
        }
    }
    var today = new Date('<?php echo date('Y-m-d') ?>');
    $(document).ready(function() {
        var target = jQuery('.gantt-chart-wrapper').find('#month_' + (today.getMonth() + 1) + '_' + today.getFullYear());
        if( target.length ){
            jQuery('.gantt-chart-wrapper').scrollTo( target, true, null );
        }
        //24/10/2013 huy thang
        stylevalidation('#ProjectAmrMdVariance');
        stylevalidation('#ProjectAmrVariance');

        function stylevalidation (id) {
            var calculatior = parseFloat($(id).val());
            if (calculatior > 0) {
                $(id).addClass('setvalidation');
            }else{
                $(id).removeClass('setvalidation');
            }
        }
        //24/10/2013 huy thang

        var height;
        $('#ProjectAmrProjectAmrSolution,#ProjectAmrProjectAmrRiskInformation,#ProjectAmrProjectAmrProblemInformation,#ProjectAmrProjectAmrSolutionDescription').focus(function(){
            $(this).tooltip('disable');
            height = $(this).height();
            $(this).stop().animate({height : '150'} , 1000);
        }).mouseup(function(){
            $(this).tooltip('close');
        }).blur(function(){
            $(this).tooltip('option' , 'content' , $(this).val());
            $(this).tooltip('enable');
            $(this).stop().animate({height : height}, 1000 , function(){
                $(this).css({height : ''});
            });
        }).tooltip({maxWidth : 1000, maxHeight : 300,content: function(target){
                return $(target).text();
            }});

        $("#ProjectAmrMdValidated,#ProjectAmrMdEngaged,#ProjectAmrMdForecasted").blur(function(){
            $(this).toNumber();
            var mdForecasted = $("#ProjectAmrMdForecasted").val().replace(/\$|\,/g,'');
            var mdValidated = $("#ProjectAmrMdValidated").val().replace(/\$|\,/g,'');
            $("#ProjectAmrMdVariance").val(mdForecasted - mdValidated);

        });
        $('#ProjectAmrValidated, #ProjectAmrEngaged, #ProjectAmrForecasted').blur(function()
        {
            $(this).toNumber();
            if($(this).val()=='')
                $(this).val('0.00');
            $(this).formatCurrency({ symbol:"" });
            //var Forecasted = $("#ProjectAmrForecasted").val().replace(/\$|\,/g,'');
            //var Validated = $("#ProjectAmrValidated").val().replace(/\$|\,/g,'');

            var Budget = $("#ProjectAmrValidated").val().replace(/\$|\,/g,'');
            var Engaged = $("#ProjectAmrEngaged").val().replace(/\$|\,/g,'');
            var Remain = $("#ProjectAmrForecasted").val().replace(/\$|\,/g,'');

            Budget = parseFloat(Budget);
            Engaged = parseFloat(Engaged);
            Remain = parseFloat(Remain);
            // console.log(Engaged);

            //24/10/2013 huy thang
            var calculatior = Engaged + Remain - Budget;
            if (calculatior > 0) {
                $("#ProjectAmrVariance").addClass('setvalidation');
            }else{
                $("#ProjectAmrVariance").removeClass('setvalidation');
            }

            $("#ProjectAmrVariance").val(Engaged + Remain - Budget).formatCurrency({ symbol:"",negativeFormat: '%s - %n'  });
            //24/10/2013 huy thang
        });

        var tabs = $(".wd-tab");
        var tab_a_selector = 'ul.ui-tabs-nav a';
        var tab_a_active = 'li.ui-state-active a';
        var cache = {};
        //tabs.tabs({event: 'change'});
        //   tabs.tabs({
        //    cache: true,
        //    event: 'change'
        //    });

        var state = {};
        var idx;

        var current_url =  document.URL ;
        tmp_p = current_url.substr(current_url.indexOf("#"),current_url.length);
        $("#project_tab_index").val(tmp_p);
        var p_tab_index = $("#project_tab_index").val();

        var check_multi_selected_deliverable = false;
        var check_multi_selected_evolution = false;

        tabs.find( tab_a_selector ).click(function(){
            var selected = $( ".wd-tab" ).tabs( "option", "selected" );
            var tab_index = (selected+1);
            $("#project_tab_index").val("#wd-fragment-"+tab_index);
            $("#flashMessage").hide();
            if(tab_index==9){
                if(!check_multi_selected_deliverable){
                    $("#ProjectLivrableActor").multiSelect({noneSelected: 'Select actors', oneOrMoreSelected: '*', selectAll: false });
                    check_multi_selected_deliverable = true;
                }
            }

            if(tab_index==10){
                if(!check_multi_selected_evolution){
                    $("#ProjectProjectEvolutionImpactId").multiSelect({noneSelected: 'Select impacts', oneOrMoreSelected: '*', selectAll: false });
                    check_multi_selected_evolution = true;
                }
            }
        });

        if(p_tab_index=="#wd-fragment-9"){
            if(!check_multi_selected_deliverable){
                $("#ProjectLivrableActor").multiSelect({noneSelected: 'Select actors', oneOrMoreSelected: '*', selectAll: false });
                check_multi_selected_deliverable = true;
            }
        }

        if(p_tab_index=="#wd-fragment-10"){
            if(!check_multi_selected_evolution){
                $("#ProjectProjectEvolutionImpactId").multiSelect({noneSelected: 'Select impacts', oneOrMoreSelected: '*', selectAll: false });
                check_multi_selected_evolution = true;
            }
        }

        //$(".wd-table table").dataTable();

        /*$('#ProjectAmrProjectAmrMepDate').datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        });*/

        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }


        $('#btnSave').click(function(){
            $("#flashMessage").hide();
            $('div.error-message').remove();
            $("div.wd-input input, select").removeClass("form-error");
            $('#ProjectAmrIndexPlusForm').submit();
            return false;
        });
        $('#ProjectAmrProjectAmrProgression').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#ProjectAmrProjectAmrProgression').val();
            $('div.error-message').remove();
            if(!rule.test(x)||x<0||x>100){
                var fomrerror = $("#ProjectAmrProjectAmrProgression");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php __("The Progression must be a number 0-100 ") ?>"+'</div>');
            }
            else{
                var fomrerror = $("#ProjectAmrProjectAmrProgression");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });

        function isOnLimit(elementId,top,bottom,notify){
            var val = $("#"+elementId).val();
            if(isNumber(val)){
                if(top=='vc'){
                    if(bottom=='vc'){
                        return true;
                    }
                    else{
                        if(val>=bottom) return true;
                    }
                }
                else{
                    if(bottom=='vc'){
                        if(val<=top) return true;
                    }
                    else{
                        if(val>=bottom && val<=top) return true;
                    }
                }
            }
            NotifyError(elementId,notify);
            return false;
        }

        function NotifyError(elementId,notify){
            if(notify=='') notify = "This field must be between the limit.";
            var endDate = $("#"+elementId);
            endDate.addClass("form-error");
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+notify+'</div>');
        }

        $("#reset_button").click(function(){
            $("#project_team_id").val("");
            $("#title_form_update").html("<?php __("Add a new employee for this project") ?>");
            $("#project_phase_plan_id").val("");
            $("#title_form_update_phase").html("<?php __("Add a new phase planning for this project") ?>");
            $("#project_milestone_id").val("");
            $("#title_form_update_milestone").html("<?php __("Add a new milestone for this project") ?>");
            $("#project_task_id").val("");
            $("#title_form_update_task").html("<?php __("Add a new task for this project") ?>");
            $("#project_risk_id").val("");
            $("#title_form_update_risk").html("<?php __("Add a new risk for this project") ?>");
            $("#project_issue_id").val("");
            $("#title_form_update_issue").html("<?php __("Add a new issue for this project") ?>");
            $("#project_decision_id").val("");
            $("#title_form_update_decisions").html("<?php __("Add a new decision for this project") ?>");
            $("#ProjectLivrableActor span").html("<?php __("Select actors") ?>");
            $("input[name='ProjectLivrableActor[]']").removeAttr("checked");
            $("input[name='ProjectLivrableActor[]']").parent().removeClass("checked");
            $("#project_livrable_id").val("");
            $("#title_form_update_livrable").html("<?php __("Add new a deliverable for this project") ?>");
            $("#ProjectProjectEvolutionImpactId span").html("<?php __("Select impact") ?>");
            $("input[name='ProjectProjectEvolutionImpactId[]']").removeAttr("checked");
            $("input[name='ProjectProjectEvolutionImpactId[]']").parent().removeClass("checked");
            $("#project_evolution_id").val("");
            $("#title_form_update_evolution").html("<?php __("Add a new evolution for this project") ?>");
            // HuyTD: Quick reset form command
            $("[name*='data[ProjectAmr]']").val("");
        });


        // Script for  subprogram
        $("#ProjectAmrProjectAmrSubProgramId").attr("disabled", "disabled");
        if($.trim($("#ProjectAmrProjectAmrProgramId").val()!="")){
            var id = $("#ProjectAmrProjectAmrProgramId").val();
            var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_program_id'])) ? $this->data['ProjectAmr']['project_amr_sub_program_id'] : "" ?>';
            $.ajax({
                url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id +'/'+current_id,
                beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectAmrProjectAmrSubProgramId").html(data);
                    $("#ProjectAmrProjectAmrSubProgramId").removeAttr("disabled");


                }
            });
        }

        $("#ProjectAmrProjectAmrProgramId").change(function(){
            var id = $(this).val();
            var program_current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_program_id'])) ? $this->data['ProjectAmr']['project_amr_program_id'] : ""; ?>';
            $("#ProjectAmrProjectAmrSubProgramId").attr("disabled", "disabled");
            if(id == program_current_id){
                var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_program_id'])) ? $this->data['ProjectAmr']['project_amr_sub_program_id'] : "" ?>';
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id + "/"+current_id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubProgramId").html(data);
                        $("#ProjectAmrProjectAmrSubProgramId").removeAttr("disabled");
                    }
                });
            }else{
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubProgramId").html(data);
                        $("#ProjectAmrProjectAmrSubProgramId").removeAttr("disabled");
                    }
                });
            }

        });

        $("#ProjectAmrProjectAmrSubCategoryId").attr("disabled", "disabled");
        if($.trim($("#ProjectAmrProjectAmrCategoryId").val()!="")){
            var id = $("#ProjectAmrProjectAmrCategoryId").val();
            var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_category_id'])) ? $this->data['ProjectAmr']['project_amr_sub_category_id'] : "" ?>';
            $.ajax({
                url: '<?php echo $html->url('/project_amrs/get_sub_categories/') ?>' + id +"/"+current_id ,
                beforeSend: function() { $("#ProjectAmrProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectAmrProjectAmrSubCategoryId").html(data);
                    $("#ProjectAmrProjectAmrSubCategoryId").removeAttr("disabled");
                }
            });
        }

        $("#ProjectAmrProjectAmrCategoryId").change(function(){
            var id = $(this).val();
            var cate_current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_category_id'])) ? $this->data['ProjectAmr']['project_amr_category_id'] : "" ?>';
            $("#ProjectAmrProjectAmrSubCategoryId").attr("disabled", "disabled");
            if(id == cate_current_id){
                var current_id = '<?php echo (!empty($this->data['ProjectAmr']['project_amr_sub_category_id'])) ? $this->data['ProjectAmr']['project_amr_sub_category_id'] : "" ?>';
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_categories/') ?>' + id + "/"+ current_id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubCategoryId").html(data);
                        $("#ProjectAmrProjectAmrSubCategoryId").removeAttr("disabled");
                    }
                });
            }else{
                $.ajax({
                    url: '<?php echo $html->url('/project_amrs/get_sub_categories/') ?>' + id,
                    beforeSend: function() { $("#ProjectAmrProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#ProjectAmrProjectAmrSubCategoryId").html(data);
                        $("#ProjectAmrProjectAmrSubCategoryId").removeAttr("disabled");
                    }
                });
            }

        });

        try {
            oHandler = $(".mydds").msDropDown().data("dd");
            $("#ver").html($.msDropDown.version);
        } catch(e) {
            alert("Error: "+e.message);
        }
    });
    /**
     *  Set time paris
     */
    setAndGetTimeOfParis = function(){
        //var _date = new Date().toLocaleString('en-US', {timeZone: 'Europe/Paris'}); // khong dung dc tren IE
        var _date = new Date(); // Lay Ngay Gio Thang Nam Hien Tai
        /**
         * Lay Ngay Gio Chuan Cua Quoc Te
         */
        var _day = _date.getUTCDate();
        var _month = _date.getUTCMonth() + 1;
        var _year = _date.getUTCFullYear();
        var _hours = _date.getUTCHours();
        var _minutes = _date.getUTCMinutes();
        var _seconds = _date.getUTCSeconds();
        var _miniSeconds = _date.getUTCMilliseconds();
        /**
         * Tinh gio cua nuoc Phap
         * Nuoc Phap nhanh hon 2 gio so voi gio Quoc te.
         */
        _hours = _hours + 2;
        if(_hours > 24){
            _day = _day + 1;
            if(_day > daysInMonth(_month, _year)){
                _month = _month + 1;
                if(_month > 12){
                    _year = _year + 1;
                }
            }
        }
        _day = _day < 10 ? '0'+_day : _day;
        _month = _month < 10 ? '0'+_month : _month;
        return _hours + ':' + _minutes + ' ' + _day + '/' + _month + '/' + _year;
    };
    /**
     * Add log system of sale lead
     */
    var companyName = <?php echo json_encode($companyName);?>,
        company_id = <?php echo json_encode($company_id);?>,
        employeeLoginName = <?php echo json_encode($employeeLoginName);?>,
        employeeLoginId = <?php echo json_encode($employeeLoginId);?>;

    var avatar = <?php echo json_encode($avatarEmployeeLogin);?>;
    function addLog(id){
        var nl = $(id).find('.new-log').toggle();
        if( nl.is(':visible') ){
            nl.find('.log-content').focus();
        }
    }
    function updateLog(model){
        var inp = $(this),
            li = $(this).closest('li');
        var value = $.trim(inp.val()),
            log_id = li.data('log-id');
        if( value ){
            // loading
            inp.prop('disabled', true);
            // save
            $.ajax({
                url: '<?php echo $html->url(array('action' => 'update_data_log')) ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    data: {
                        id: log_id,
                        company_id: company_id,
                        model: model,
                        model_id: projectName['id'],
                        name: li.find('.log-author').text(),
                        description: value,
                        employee_id: employeeLoginId,
                        update_by_employee: employeeLoginName
                    }
                },

                success: function(response) {
                    var data = response.LogSystem;
                    if( !log_id ){
                        var newLi = li.clone();
                        newLi.removeClass('new-log');
                        newLi.find('.log-content').prop('rowspan', 1);
                        newLi.find('.log-time').text(data.time);
                        newLi.data('log-id', data.id);
                        newLi.insertAfter(li);
                        // reset li
                        inp.val('').prop('disabled', false);
                        // reset new Li
                        inp = newLi.find('.log-content');
                        // hide
                        li.hide();
                    }
                },
                complete: function(){
                    // hide loading
                    // can change
                    inp.prop('disabled', false).css('color', '#3BBD43');
                }
            });
        }
    }

    // Milestones
    var bandwidth = $('.gantt .gantt-ms .gantt-line').width();
    var stack =  [],height = 16,icon = 16;
    $('.gantt-line .gantt-msi').each(function(){
        var $element = $(this);
        var $span = $element.find('span');

        var left = $element.position().left;
        var width = $span.width();
        var row = 0;

        if(left+width+icon >= bandwidth ){
            left -= (width + icon) * 2;
            $span.css('marginLeft' , - (width + icon ));
        }
        $(stack).each(function(k,v){
            if(left >= v){
                return false;
            }
            row++;
        });
        stack[row] = left+width+icon;
        $element.css('top' , row* height);
    });
    $('.gantt-ms .gantt-line').height(stack.length * height );
   $(document).on('click', '.weather', function(e){
        var t = $(this);
        t.parent().children('.weather').prop('disabled', true);
        t.closest('.acceptance').children('.acceptance-name').css('color', 'gray');
        $.ajax({
            url: '<?php echo $this->Html->url('/') ?>project_acceptances/updateWeather',
            type: 'POST',
            data: {
                data: {
                    id : t.data('id'),
                    project_id: <?php echo $project_id ?>,
                    weather: t.val()
                }
            },
            complete: function(){
                setTimeout(function(){
                    t.closest('.acceptance').children('.acceptance-name').css('color', 'rgb(59, 189, 67)');
                    t.parent().children('.weather').prop('disabled', false);
                }, 500);
            }
        });
    });
    function exportKPI(){
        // hide button
    }
    function resetKPI(){

    }
    function SubmitDataExport(){
        $('.kpi-log').css('max-height', 'none');
        $('.export-hidden').hide();
        $('.gantt-wrapper').css('width', 'auto');
        $('#chart-wrapper').css('width', '75%');
        $('#chart-wrapper').html2canvas();
        setTimeout(function(){
            $('.kpi-log').css('max-height', '160px');
            $('.gantt-wrapper').css('width', '86%');
            $('#chart-wrapper').css('width', '98%');
            $('.export-hidden').show();
        }, 5000);
    }
    $(".input_weather").on('change', function(){
        var field = $(this).attr('name');
        var value = $(this).val();
        $.ajax({
            url: '/project_amrs/updateWeather/',
            type: 'POST',
            data: {
                data: {
                    project_id : <?php echo $project_id ?>,
                    field: field,
                    value: value
                }
            },
        });
    });
    (function($){
        $("#right, #left").click(function() {
            var dir = this.id=="right" ? '+=' : '-=' ;
            $(this).closest(".budget-progress").find('#budget-inner').stop().animate({scrollLeft: dir+'500'}, 1000);
        });
        
    })(jQuery);
</script>

<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
