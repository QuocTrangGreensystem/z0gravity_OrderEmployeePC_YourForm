<?php //echo $html->css('jquery.dataTables');   ?>
<?php echo $html->css(array('gantt_v2','project_staffing_visions','demo_table')); ?>
<?php //echo $html->script('history_filter'); //MODIFY BY VINGUYEN 19/05/2014 ?>
<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'month';
?>

<!--[if lt IE 9]>
<?php echo $html->script('flash_canvas/flashcanvas'); ?>
<script type="text/javascript">
    var _createElement = document.createElement;
    document.createElement = function(n){
        var element = _createElement.call(this,n);
        if(n=="canvas"){
            document.getElementById("target").appendChild(element);
            FlashCanvas.initElement(element);
        }
        return element;
    };
</script>
<div id="target" style="position: absolute; top: -10000px;left: -999999px;"></div>
<![endif]-->

<?php
echo $html->script(array('html2canvas', 'jquery.html2canvas'));
echo $html->css('jquery.mCustomScrollbar');
echo $html->script(array('jquery.easing.1.3', 'jquery.mCustomScrollbar'));
?>
<style>
	.export {
		display: block;
		float: right;
		width: 32px;
		height:32px;
		background: url(/img/export.jpg);
	}
    .resource_theoretical div{
        font-size: 11px;
        word-break:keep-all;
    }
    .gantt-head{
        text-align:center;
        border-bottom:1px solid #ccc;
        /*margin-bottom:-1px !important;*/
    }
    .gantt-head div{
        text-align:center !important;
    }
    .gantt-day div{
        width: 62px !important;
        text-align:center !important;
    }
    .gantt-day{
        background-color:#185790 !important;
        color:#FFF;
        font-weight:bold;
    }
    .gantt-input, .gantt-input div{
        width: 62px !important;
    }
    .gantt-summary .gantt-num td{
          border-width: 0 1px 1px 0;
    }
    .total-working {
        height: 23px;
    }
    .box-left, .box-right {
    }
    .box-left.box-left-ajax .gantt-node.gantt-employee.gantt-child {
        border: 0 !important;
    }
</style>
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <script type="text/javascript">
        $(function(){
            //$('#dialog_vision_staffing_news_menu').dialog('open');
        });
    </script>
<?php /*
<a href="#" onclick="SubmitDataExport();return false;" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>
*/
$url = $this->params['url'];
unset($url['url'], $url['ext']);
$url['pr_file'] = 1;
?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title" style="margin-top: 10px;">
                	<?php
					$exportUrl=$_SERVER["REQUEST_URI"];
					$exportUrl = str_replace('pr_file=0','pr_file=1',$exportUrl);
					?>

                    <?php
                        $resetStaffings = !empty($staffings) ? Set::combine($staffings, '{n}.id', '{n}.name') : array();
                        $currentPc = isset($arrGetUrl['ItMe']) ? $arrGetUrl['ItMe'] : 0;
                        if(isset($arrGetUrl['target']) && $arrGetUrl['target'] == 1):
                            $namePc = !empty($resetStaffings[$currentPc]) ? $resetStaffings[$currentPc] : '';
                    ?>
                        <a href="#" style="vertical-align: middle" id="absence-prev" onclick="window.history.go(-1); return false;"><span>Back</span></a>
                        <h1 style="display: inline-block"><?php echo __($namePc);?></h1>
                    <?php
                        endif;
                    ?>
                	<a href="<?php echo $exportUrl; ?>" target="_blank" class="export-excel-icon-all" id="export-submit" style="vertical-align: middle" title="<?php __('Export Excel')?>"><span><?php __('Export Excel') ?></span></a>
                    <img src="<?php echo $this->Html->url('/') ?>img/loading_check.gif" alt="" id="loading" style="display: none" />
                    <div style="float: left; display: none">
                        <?php
                        echo $this->Form->create('Display', array('type' => 'get', 'url' => array_merge(array(
                                'controller' => 'activity_tasks',
                                'action' => 'visions_staffing'
                            ))));
                        ?>
                        <div id="gantt-display">
                            <label class="title"><?php __('Display real time'); ?> </label>
                            <?php
                            echo $this->Form->input('display', array(
                                'rel' => 'no-history',
                                'onchange' => 'jQuery(this).closest(\'form\').submit();',
                                'value' => $display,
                                'options' => array(__('No', true), __('Yes', true)),
                                'type' => 'radio', 'legend' => false, 'fieldset' => false
                            ));
                            foreach ($arg["?"] as $key => $val) {
                                if ($key == 'display') {
                                    continue;
                                }
                                echo $this->Form->hidden($key, array('value' => $val));
                            }
                            ?>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
                <div id="GanttChartDIV" class="ganttCustom">
                    <?php
                    $rows = 0;
                    $start = $end = 0;
                    $data = $projectId = $conditions = array();
                    $resetStaffings = array();

                    foreach ($activities as $activitie) {
                        $_data = array(
                            'name' => '<b>AAA</b>'.$activitie['Activity']['name'],
                            'phase' => array(
                                'name' => $activitie['Activity']['name'],
                                'start' => $activitie['Activity']['start_date'],
                                'end' => $activitie['Activity']['end_date'],
                                'rstart' => $activitie['Activity']['start_date'],
                                'rend' => $activitie['Activity']['end_date'],
                                'color' => '#004380'
                            )
                        );
                        if ($_data['phase']['rstart'] > 0) {
                            $_start = min($_data['phase']['start'], $_data['phase']['rstart']);
                        } else {
                            $_start = $_data['phase']['start'];
                        }
                        if (!$start || ($_start > 0 && $_start < $start)) {
                            $start = $_start;
                        }
                        $_end = max($_data['phase']['end'], $_data['phase']['rend']);
                        if (!$end || $_end > $end) {
                            $end = $_end;
                        }
                        $data[] = $_data;
                    }
                    $summary = isset($this->params['url']['summary']) ? $this->params['url']['summary'] : false;

                    $showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
                    $start = !empty($startDateFilter) ? $startDateFilter : $start;
                    $end = !empty($endDateFilter) ? $endDateFilter : $end;
                    if (empty($start) || empty($end)) {
                        echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
                    } else {
						$listDays = $dateType == 'week' ? $listWeeks : $listDays;
                        $type = array(
                            'type' => $dateType, // truyen kieu hien thi date, week, month, year
                            'dateTypes' => $listDays // danh sach ngay lam viec tu start date -> end date, lay tu libs
                        );
                        $this->GanttVs->create($type, $start, $end, array(), false); // thay strtotime('01-01-2015') = start date, strtotime('31-01-2015') = end date.
                        // change in here
						$isCheck=isset($isCheck)?$isCheck:false;
						$newDataStaffings=isset($newDataStaffings)?$newDataStaffings:array();
						$staffings=isset($staffings)?$staffings:false;
						$summary=isset($summary)?$summary:array();
						$showType=isset($showType)?$showType:false;
						$staffingsTmp = $staffings;
						$staffings = array();
						if( ($showType == 1 && $isCheck == false) || ($showType == 0 && $isCheck == 1) || ($showType == 0 && ($isCheck == 2 || $isCheck == 0)) || ($showType == 5 && $isCheck == 2) || ($showType == 5 && $isCheck == 1))
						{
							$staffings['companyConfigs']=$companyConfigs;
						}
						$staffings['data']=$staffingsTmp;
						$staffings['dateType']=$dateType;
                        $staffings['budgetTeam']=$budgetTeam;
                        echo $this->Html->scriptBlock('GanttData = ' . $this->GanttVs->drawStaffing($staffings, $summary, $showType, $isCheck, $newDataStaffings, $activityType));
                        $this->GanttVs->end();
                    }
                    ?>
                    <div style="clear: both;"></div>
                </div>
                <?php
                if (!empty($start) && !empty($end) && empty($staffings)) {
                    echo $this->Html->tag('h1', __('No data exist to create staffing', true), array('style' => 'color:red'));
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
//debug($activityType);exit;
echo $this->Form->create('Export', array('url' => array('controller' => 'project_staffings', 'action' => 'export_project'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
echo $this->Form->hidden('rows', array('value' => $rows));
echo $this->Form->hidden('start', array('value' => $start));
echo $this->Form->hidden('end', array('value' => $end));
echo $this->Form->hidden('summary', array('value' => $summary));
echo $this->Form->hidden('showGantt', array('value' => $showGantt));
echo $this->Form->hidden('showType', array('value' => $showType));
echo $this->Form->hidden('conditions', array('value' => serialize($conditions)));
echo $this->Form->hidden('projectId', array('value' => serialize($projectId)));
echo $this->Form->hidden('months', array('value' => serialize($this->GanttVs->getMonths())));
echo $this->Form->hidden('displayFields', array('value' => '0'));
echo $this->Form->end();
?>

<!-- Dialog Export -->
<div id="dialog_vision_export" class="buttons" title="<?php __("Display Fields") ?>" style="display: none;">
    <fieldset>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="input">
                <?php
                echo $this->Form->input('displayFields', array(
                    'div' => false,
                    'label' => false,
                    'name' => 'displayFields',
                    'empty' => false,
                    'id' => 'dialog_vision_export_fields',
                    'multiple' => 'checkbox',
                    'hiddenField' => false,
                    // 'style' => 'margin-right:11px; width:52% !important',
                    'value' => 0,
                    "options" => array(
                        0 => __("All fields", true),
                        'estimation' => __('Estimation', true),
                        'validated' => __('Validated', true),
                        'remains' => __('Postponed', true),
                        'consumed' => __('Consumed', true),
                        'forecast' => __('Forecast', true)
                        )));
                ?>
            </div>
        </div>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel" id="no_port"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_port"><?php __('OK') ?></a></li>
    </ul>
</div>
<div class="scroll-bar"><div class="content-scroll"></div></div>
<div class="settingBox">
<img onclick="resizeBox('out')" src="<?php echo $this->Html->url('/img/front/btn_zoom_out.gif')?>" alt="Zoom in" />
<img onclick="resizeBox('in')" src="<?php echo $this->Html->url('/img/front/btn_zoom_in.gif')?>" alt="Zoom in" />
</div>
<?php
	$currentUrl=$_SERVER["REQUEST_URI"];
	$currentUrl=str_replace('/activity_tasks/visions_staffing','visions_staffing',$_SERVER["REQUEST_URI"]);
	$currentUrl=$currentUrl.'&ajax=1';
	$currentUrl=str_replace('summary=0','summary=1',$currentUrl);
	$currentUrlEmployee=str_replace('type=1','type=0',$currentUrl);
	$currentUrlResource=$currentUrlEmployee.'&getResource=1';
	$currentUrlActivity=$currentUrlEmployee.'&getActivity=1';
	//SET COL
	$colCapacityByYears = ($showType == 0 && $isCheck == 1) || (isset($arrGetUrl['target']) && $arrGetUrl['target'] == 1) ? 4 : 5;
	/*if($showType == 5)
	{
		$colCapacityByYears = $isCheck == 1 ? 4 : 5;
	}*/
	$displayAbsence = isset($companyConfigs['staffing_by_pc_display_absence']) ? $companyConfigs['staffing_by_pc_display_absence'] : false;
	$displayWorkingDay = isset($companyConfigs['staffing_by_pc_display_working_day']) ? $companyConfigs['staffing_by_pc_display_working_day'] : false;
	$displayRealCapacity = isset($companyConfigs['staffing_by_pc_display_real_capacity']) ? $companyConfigs['staffing_by_pc_display_real_capacity'] : false;
	$displayRealFte = isset($companyConfigs['staffing_by_pc_display_real_fte']) ? $companyConfigs['staffing_by_pc_display_real_fte'] : false;
	$displayTheoreticalCapacity = isset($companyConfigs['staffing_by_pc_display_capacity_theoretical']) ? $companyConfigs['staffing_by_pc_display_capacity_theoretical'] : false;
	$displayTheoreticalFte = isset($companyConfigs['staffing_by_pc_display_theoretical_fte']) ? $companyConfigs['staffing_by_pc_display_theoretical_fte'] : false;
	if($dateType == 'day' || $dateType == 'week')
	{
		$displayTheoreticalCapacity = false;
		$displayTheoreticalFte = false;
	}
	if(isset($staffings['companyConfigs']))
	{
		$colCapacityByYears = $displayAbsence ? $colCapacityByYears+1 : $colCapacityByYears+0;
		$colCapacityByYears = $displayWorkingDay ? $colCapacityByYears+1 : $colCapacityByYears+0;
		$colCapacityByYears = $displayRealCapacity ? $colCapacityByYears+1 : $colCapacityByYears+0;
		$colCapacityByYears = $displayRealFte ? $colCapacityByYears+1 : $colCapacityByYears+0;
		$colCapacityByYears = $displayTheoreticalCapacity ? $colCapacityByYears+1 : $colCapacityByYears+0;
		$colCapacityByYears = $displayTheoreticalFte ? $colCapacityByYears+1 : $colCapacityByYears+0;
	}
    $rowspanOne = 3;
    if($dateType == 'month' && $showType == 0 && $budgetTeam){
        $colCapacityByYears += 2;
        $rowspanOne += 1;
    }
	//debug($colCapacityByYears);exit;
	//END
?>
<style>
    .box-left, .box-right{
		border:1px solid #F00;
		overflow:hidden;
		height:300px;
		margin-bottom:22px;
	}
	.box-left{
		border-right:none !important;
	}
	.box-right{
		border-right:left !important;
	}
	.box-left.acti, .box-right.acti{
		border:none !important;
		overflow:hidden !important;
		height:auto !important;
	}
	.scroll-bar{
		overflow-y:scroll;
		height:300px;
		width:17px !important;
		top:50%;
		left:0;
		background-color:transparent;
		position:absolute;
		display:none;
	}
	.content-scroll{
		width:17px !important;
	}
	.settingBox{
		position:absolute; left:0; width:60px; height:26px; background-color:#FFF; z-index:10; border:1px solid #F00; border-style:solid solid none solid; display:none;
	}
	.settingBox img{
		cursor:pointer;
	}
	<?php if($activityType==1 || $showType==5)
	{ ?>
		.trFamily{
			display:none;
		}
	<?php }
	elseif($activityType==0 && $showType==0)
	{ ?>
		.trActivity .gantt-name div{
			background: url("../img/slick_grid/collapse.gif") left no-repeat !important;
		}
		.trActivity.acti .gantt-name div{
			background: url("../img/slick_grid/expand.gif") left no-repeat !important;
		}
		.trEmployee.trActivity{
			display: table-row;
		}
	<?php } ?>
	<?php
	if(($showType==0)||$showType==1)
	{ ?>
	.percent{
		display:table-cell;
	}
	.box-left-ajax .percent div a{
		display:none;
	}
	.box-left-ajax .gantt-capacity, .box-right-ajax .gantt-capacity{
		display:none;
	}
	.box-left-ajax .trEmployee .gantt-name div {
		background:none !important;
	}
	<?php } ?>
	#staffing-chart-btn, #staffing-chart{
		width: 800px;  margin: 0 auto
	}
	#staffing-chart-btn{
		padding-left:20px;
		margin-bottom:0px;
		position:relative;
		z-index:1000;
	}
	#staffing-chart-btn span{
		font-size:14px;
		padding-right:30px;
	}
	.ui-dialog-titlebar-close {
		visibility: visible;
	}
	<?php
	if(isset($arrGetUrl['target']) && $arrGetUrl['target'] == 1)
	{
		?>
		.box-left, .box-right{
			height:auto !important;
			border:none !important;
		}
		.settingBox{
			display:none !important;
		}
		.scroll-bar{
			display:none !important;
		}
		<?php
	}
	?>
</style>
<!-- Dialog Export -->
<!-- Dialog Export -->
<?php echo $html->script('chart/highcharts.js'); ?>
<?php echo $html->script('chart/exporting.js'); ?>

<div id="dialog_staffing_screen" style="display:none">
	<div id="staffing-chart-btn"></div>
	<div id="staffing-chart"></div>
</div>

<?php
if( $dateType == 'month' ){
    $ddasdad = new DateTime();
    $ddasdad->setTimestamp($endDateFilter);
    $ddasdad->modify('last day of this month');
    $ed = $ddasdad->format('d-m-Y');
    $st = date('d-m-Y', $startDateFilter);
} else {
    $ed = $_GET['aEndDate'];
    $st = $_GET['aStartDate'];
}
?>
<script type="text/javascript">
    var exporterReady = false;
    var ids = [],
        parent = {};
    var start = <?php echo json_encode($st) ?>,
        end = <?php echo json_encode($ed) ?>,
        viewby = <?php echo json_encode($dateType) ?>,
        type = <?php echo json_encode($showType) ?>,
        showSummary = <?php echo json_encode($summary) ?>,
        family = <?php echo json_encode(isset($_GET['aFamily']) ? implode(',', $_GET['aFamily']) : '') ?>,
        subfamily = <?php echo json_encode(isset($_GET['aSub']) ? implode(',', $_GET['aSub']) : '') ?>,
        activity = <?php echo json_encode(isset($_GET['aName']) ? implode(',', $_GET['aName']) : '') ?>,
        customer = <?php echo json_encode(isset($_GET['aCustomer']) ? implode(',', $_GET['aCustomer']) : '') ?>,
        pc = <?php echo json_encode(isset($_GET['aPC']) ? implode(',', $_GET['aPC']) : '') ?>,
        resource = <?php echo json_encode(isset($_GET['aEmployee']) ? implode(',', $_GET['aEmployee']) : '') ?>,
        priority = <?php echo json_encode(isset($_GET['priority']) ? $_GET['priority'] : '') ?>,
        onlysummary = !type && !resource,
        is_resource = !type && resource;
	var wh=screen.height;
	var $sessionGraph={};
	var $sessionGraphMonth={};
    var rowspanOne = <?php echo json_encode($rowspanOne);?>;
    var curPC;
    <?php
    $query = $this->params['url'];
    unset($query['url'], $query['ext']);
    ?>
    var loadOnce = false,
        startDate = <?php echo $startDateFilter ?>,
        endDate = <?php echo $endDateFilter ?>,
        buildS = true;;
	function showLoader(id) {
		jQuery("<span class='showLoader'><img src='<?php echo $this->Html->url('/img/ajax-loader.gif'); ?>' ></span>").insertAfter("#"+id+" .gantt-name div");
		block = true;
	}
	function showGraph(emp,idFamily,type){
		var htmlBtn='';
		_check0=_check1=_check2=_check3='';

		if(type=='consumed')
		{
			_check0='checked="checked"';
		}
		else if(type=='workload')
		{
			_check1='checked="checked"';
		}
		else if(type=='mdconsumed')
		{
			_check2='checked="checked"';
		}
		else if(type=='mdworkload')
		{
			_check3='checked="checked"';
		}
		htmlBtn+='<input type="radio" '+_check0+' name="pie-chart" onclick=\'showGraph('+emp+',"'+idFamily+'","consumed");\'  /> <span class="title-chart">% <?php echo __('Consumed',true);?> </span>';
		htmlBtn+='<input type="radio" '+_check1+' name="pie-chart" onclick=\'showGraph('+emp+',"'+idFamily+'","workload");\'  /> <span class="title-chart">% <?php echo __('Workload',true);?> </span>';
		htmlBtn+='<input type="radio" '+_check2+' name="pie-chart" onclick=\'showGraph('+emp+',"'+idFamily+'","mdconsumed");\'  /> <span class="title-chart"><?php echo __('M.D',true); echo ' '; echo __('Consumed',true);?> </span>';
		htmlBtn+='<input type="radio" '+_check3+' name="pie-chart" onclick=\'showGraph('+emp+',"'+idFamily+'","mdworkload");\'  /> <span class="title-chart"><?php echo __('M.D',true); echo ' '; echo __('Workload',true);?> </span>';
		$('#staffing-chart-btn').html(htmlBtn);

		if(type=='consumed'||type=='workload')
		{
			<?php
			if($showType==1)
			{
			 ?>
				dataGraph=$sessionGraph;
				if(dataGraph===null) return false;
			<?php
			}
			else
			{ ?>
				var dataGraph='<?php echo isset($_SESSION['graph'])?json_encode($_SESSION['graph']):array(); ?>';
				dataGraph=JSON.parse(dataGraph);

			<?php } ?>
			//dataGraph=JSON.parse(dataGraph);
			//console.log(dataGraph);
			var dataOfEmp=[];
			//return false;
			if(idFamily==0)
			{
				if(type=='consumed')
				{
					$.each(dataGraph[emp], function(family,val){
						percent=parseFloat(val.consumedVal,2);
						dataOfEmp.push([val.name,percent]);
					});

				}
				else
				{
					$.each(dataGraph[emp], function(family,val){
						percent=parseFloat(val.workloadVal,2);
						dataOfEmp.push([val.name,percent]);
					});
				}
			}
			else
			{
				if(type=='consumed')
				{
					$.each(dataGraph[emp], function(family,val){
						percent=parseFloat(val.consumedVal,2);
						if(family==idFamily)
						{
							dataOfEmp.push({
								name: val.name,
								y: percent,
								sliced: true,
								selected: true
							})
						}
						else
						{
							dataOfEmp.push([val.name,percent]);
						}
					});
				}
				else
				{
					$.each(dataGraph[emp], function(family,val){
						percent=parseFloat(val.workloadVal,2);
						if(family==idFamily)
						{
							dataOfEmp.push({
								name: val.name,
								y: percent,
								sliced: true,
								selected: true
							})
						}
						else
						{
							dataOfEmp.push([val.name,percent]);
						}
					});
				}
			}
			var titleVal=type;
			$('#staffing-chart').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: 1,//null,
					plotShadow: false
				},
				title: {
					text: ''
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.2f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							}
						}

					}
				},
				series: [{
					type: 'pie',
					name: titleVal,
					data: dataOfEmp
				}],
				exporting: {
					enabled: false
				}
			});
			$('#dialog_staffing_screen').dialog({
				position    :'center',
				autoOpen    : true,
				autoHeight  : true,
				modal       : true,
				width       : 800,
				open : function(e){
					//var $dialog = $(e.target);
				}
			});
		}
		else
		{
			$_arrMonth='<?php echo json_encode($_SESSION['arrMonth']); ?>';
			$_arrMonth=JSON.parse($_arrMonth);
			<?php
			if($showType==1)
			{ ?>
				dataGraph=$sessionGraphMonth;
			<?php
			}
			else
			{ ?>
				var dataGraph='<?php echo isset($_SESSION['graphMonth'])?json_encode($_SESSION['graphMonth']):array(); ?>';
				dataGraph=JSON.parse(dataGraph);
			<?php } ?>

			var dataOfEmp=[];
			if(type=='mdconsumed')
			{
				$.each(dataGraph[emp], function(family,val){
					var _dataEmp={};
					_dataEmp.name=val.name;
					_dataEmp.data=[];
					$.each(val.dataConsumed, function(_time,_val){
						_dataEmp.data.push(parseFloat(_val));
					});
					dataOfEmp.push(_dataEmp);
				});
			}
			else
			{
				$.each(dataGraph[emp], function(family,val){
					var _dataEmp={};
					_dataEmp.name=val.name;

					_dataEmp.data=[];
					$.each(val.dataWorkload, function(_time,_val){
						_dataEmp.data.push(parseFloat(_val));
					});
					dataOfEmp.push(_dataEmp);
				});
			}
			$('#staffing-chart').width(800);
			$('#staffing-chart').highcharts({
				title: {
					text: '',
					x: -20 //center
				},
				subtitle: {
					text: '',
					x: -20
				},
				xAxis: {
					categories: $_arrMonth
				},
				yAxis: {
					title: {
						text: '<?php echo __('M.D',true); ?>'
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					valueSuffix: ' <?php echo __('M.D',true); ?>'
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle',
					borderWidth: 0
				},
				series: dataOfEmp,
				exporting: {
					enabled: false
				}
			});
			$('#dialog_staffing_screen').dialog({
				position    :'center',
				autoOpen    : true,
				autoHeight  : true,
				modal       : true,
				width       : 800,
				open : function(e){
					//var $dialog = $(e.target);
				}
			});
		}
		return false;
	}
	/*$('#dialog_staffing_screen').dialog({
            position    :'center',
            autoOpen    : true,
            autoHeight  : true,
            modal       : true,
            width       : 600,
            open : function(e){
                //var $dialog = $(e.target);
            }
        });*/
    var targetCurrent = <?php echo (isset($arrGetUrl['target']) && $arrGetUrl['target'] == 1) ? json_encode('true') : json_encode('false');?>;
    var itMe = <?php echo (isset($arrGetUrl['ItMe'])) ? json_encode($arrGetUrl['ItMe']) : json_encode(0);?>;
    if(targetCurrent == 'true' && itMe != 0){
        $('#tr-'+itMe).removeAttr('onclick');
        ajaxShowPC('tr-'+itMe, itMe);
        loadOnce = true;
    }
    var PCUrl, resourceUrl;
	function ajaxShowPC(idTr,id){
		var temp;
        var currentUrl='<?php echo $currentUrl; ?>';
		if(targetCurrent == 'true' && itMe == id)
		{
			currentUrl=currentUrl+'&ItMe='+id;
			currentUrl = currentUrl.replace('target=1', 'target=0');
		}
		else if(targetCurrent == 'true' && itMe != id)
		{
			currentUrl = currentUrl.replace('ItMe', 'ItMeOld');
			currentUrl=currentUrl+'&ItMe='+id;
			currentUrl = currentUrl.replace('summary=1', 'summary=0');
			window.location.href = currentUrl;
			return false;
		}
		else
		{
			currentUrl=currentUrl+'&ItMe='+id+'&target=1';
			currentUrl = currentUrl.replace('summary=1', 'summary=0');
			window.location.href = currentUrl;
			return false;
		}
		if($("#"+idTr).hasClass('onload'))
		{
            showLoader(idTr);
			$('.p-'+idTr).remove();
			jQuery.ajax({
				url: currentUrl,
				type: "GET",
				cache: false,
				success: function (html) {
					var left='';
					var right='';
					var arrHtml=JSON.parse(html);
					$.each(arrHtml, function(key,val){
						left+=val.left;
						right+=val.right;
					});
					ajaxShowActivity(left,right,idTr,id);
                    PCUrl = currentUrl.replace('visions_staffing', 'getStaffings');
				}
			});
		}
		else
		{
			if($("#"+idTr).hasClass('acti'))
			{
				$('.box-left').addClass('acti');
				$('.box-right').addClass('acti');
				$('#div-box-left-'+idTr).removeClass('acti');
				$('#div-box-right-'+idTr).removeClass('acti');
				$('#'+idTr).removeClass('acti');
				$('.pp-'+idTr).fadeIn();
				hideShowBox(idTr,false);
				//$('.p-'+idTr).fadeIn();
			}
			else
			{
				$('.pp-'+idTr).addClass('acti');
				$('.p-'+idTr).addClass('acti');
				$('#'+idTr).addClass('acti');
				$('.p-'+idTr).fadeOut();
				hideShowBox(idTr,true);
			}
		}

	}
	function fixedScrollBar(idTr,hideFamily){
		var flag=setInterval(function(){
			if(!hideFamily)
			{
				$('.trFamily').each(function(index, element) {
					if($(this).hasClass('pp-'+idTr))
					{
						$(this).fadeIn();
					}
					else
					{
						$(this).hide();
					}
				});
				$('.trSubFamily').hide();
				$('.trActivity').hide();
			}
			$('.scroll-bar').fadeOut();
			if($('#div-box-left-'+idTr).length)
			{
				$('.scroll-bar .content-scroll').height($('#div-box-left-'+idTr+' table').height());
				var arrPosition=$('#div-box-left-'+idTr).position();
				jQuery(window).scrollTop(arrPosition.top-300);
				$('.settingBox').css({'top':arrPosition.top+170});
				$('.settingBox').css({'left':arrPosition.left+19});
				$('.settingBox').fadeIn();
				$('.scroll-bar').css({'top':arrPosition.top+198});
				$('#div-box-left-'+idTr).height(300);
				$('#div-box-right-'+idTr).height(300);
				$('.scroll-bar').height(300);
				$('.scroll-bar').fadeIn();
				clearInterval(flag);
			}

		},1000);
	}
	function resizeBox(type){
		$('.box-left').each(function(index, element) {
            if($(this).hasClass('acti'))
			{
				//do nothing
			}
			else
			{
				var $id=$(this).attr('id');
				var $this=$('#'+$id);
				var $idRight=$id.replace('left','right');
				var $thisRight=$('#'+$idRight);
				if(type=='in')
				{
					if($this.height()>800)
					return false;
					$this.height($this.height()+100);
					$thisRight.height($thisRight.height()+100);
					//$('.scroll-bar').height($this.height());
				}
				else
				{
					if($this.height()<=300)
					return false;
					$this.height($this.height()-100);
					$thisRight.height($thisRight.height()-100);

				}
				$('.scroll-bar').height($this.height());
			}
        });

	}
	function setScrollBarWhenScroll()
	{
		$('.box-left').each(function(index, element) {
            if($(this).hasClass('acti'))
			{
				//do nothing
			}
			else
			{
				$('.scroll-bar .content-scroll').height($('#'+$(this).attr('id')+' table').height());
			}
        });
	}
	$(function () {
        $(".scroll-bar").scroll(function () {
			$('.box-left').scrollTop($(".scroll-bar").scrollTop());
			$('.box-right').scrollTop($(".scroll-bar").scrollTop());
        });
    });
	function hideShowBox(id,hideIt){
		//$('.box-left').addClass('acti');
		//$('.box-right').addClass('acti');
		if(hideIt===true)
		{
			$('.scroll-bar').fadeOut();
			$('.settingBox').fadeOut();
			$('#div-box-left-'+id).fadeOut();
			$('#div-box-right-'+id).fadeOut();
			var parentId = $("#"+id).parent().parent().parent().attr('rel');
			if(typeof(parentId) !== 'undefined'){
				$('#div-box-left-'+parentId).removeClass('acti');
				$('#div-box-right-'+parentId).removeClass('acti');
				fixedScrollBar(parentId);
			}

		}
		else
		{
			$('#div-box-left-'+id).fadeIn();
			$('#div-box-right-'+id).fadeIn();
			$('.scroll-bar').fadeIn();
			$('.settingBox').fadeIn();
			var parentId = $("#"+id).parent().parent().parent().attr('rel');
			if(typeof(parentId) !== 'undefined'){
				$('#div-box-left-'+parentId).addClass('acti');
				$('#div-box-right-'+parentId).addClass('acti');
			}
			fixedScrollBar(id);
		}
	}
	function hideShowBoxResource(id,hideIt){
		$('.box-left-ajax').each(function(index, element) {
			var $id = $(this).attr('rel');
			$('#'+$id).addClass('acti')
			$('#div-box-left-'+$id).hide();
			$('#div-box-right-'+$id).hide();
		});
		if(hideIt===true)
		{
			$('.scroll-bar').fadeOut();
			$('.settingBox').fadeOut();
		}
		else
		{
			$('#'+id).removeClass('acti');
			$('#div-box-left-'+id).fadeIn();
			$('#div-box-right-'+id).fadeIn();
			$('.scroll-bar').fadeIn();
			$('.settingBox').fadeIn();
			$('.box-left-ajax .check-name').attr('rowspan', 3);
			fixedScrollBar(id,true);
		}
	}
	function ajaxShowActivity(leftPC,rightPC,idTr,id){
		var currentClass = $("#"+idTr).attr('class');
		var currentUrl='<?php echo $currentUrlActivity; ?>';
		currentUrl=currentUrl+'&ItMe='+id+'&classParent='+currentClass;
		jQuery.ajax({
			url: currentUrl,
			type: "GET",
			cache: false,
			success: function (html) {
				var left='';
				var right='';
				var arrHtml=JSON.parse(html);
				$.each(arrHtml, function(key,val){
					left+=val.left;
					right+=val.right;
				});
				ajaxShowEmployee(leftPC,rightPC,left,right,idTr,id);
				$('.box-left').addClass('acti');
				$('.box-right').addClass('acti');
				$( "#"+idTr ).removeClass('onload');
				$( "#"+idTr ).removeClass('acti');
				fixedScrollBar(idTr);
			}
		});
	}
	function ajaxShowResource(idTr,id){
		if($("#"+idTr).hasClass('onload'))
		{
			var currentClass = $("#"+idTr).attr('class');
			var currentUrl='<?php echo $currentUrlResource; ?>';
			currentUrl=currentUrl+'&ItMe='+id+'&classParent='+currentClass;
			$('.p-'+idTr).remove();
			showLoader(idTr);
			jQuery.ajax({
				url: currentUrl,
				type: "GET",
				cache: false,
				success: function (html) {
					var left='';
					var right='';
					var arrHtml=JSON.parse(html);
					$.each(arrHtml, function(key,val){
						if(key=='session')
						{
							$sessionGraph=val.graph;
							$sessionGraphMonth=val.graphMonth;
						}
						if(typeof val.left !='undefined' )
						{
							left+=val.left;
							right+=val.right;
						}
					});
                    var _colspanOne = $("#"+idTr+ '>td').attr('colspan'),
                        _classOne = $("#"+idTr+ '>td').attr('class'),
                        _colspanTwo = $("#r-"+idTr+ '>td').attr('colspan'),
                        _classTwo = $("#r-"+idTr+ '>td').attr('class');
                    $('<tr><td class=' +_colspanOne+ ' colspan=' +_colspanOne+ '><div class=\'box-left box-left-ajax\' rel="'+idTr+'" id="div-box-left-'+idTr+'"><table id="box-left-'+idTr+'">'+left+'</table></div></td></tr>').insertAfter( "#"+idTr );
                    $('<tr><td class=' +_classTwo+ ' colspan=' +_colspanTwo+ '><div class=\'box-right box-right-ajax\' rel="'+idTr+'" id="div-box-right-'+idTr+'"><table id="box-right-'+idTr+'">'+right+'</table></div></td></tr>').insertAfter( "#r-"+idTr );
					$('.showLoader').remove();
					$( "#"+idTr ).removeClass('onload');
					$( "#"+idTr ).removeClass('acti');
					hideShowBoxResource(idTr,false);
				}
			});
		}
		else
		{
			if($("#"+idTr).hasClass('acti'))
			{
				$('#'+idTr).removeClass('acti');
				hideShowBoxResource(idTr,false);
			}
			else
			{
				$('#'+idTr).addClass('acti');
				hideShowBoxResource(idTr,true);
			}
		}
	}

	<?php
	if($showType==1)
	{ ?>
	function ajaxShowEmployee(leftPC,rightPC,leftActi,rightActi,idTr,id, callback){
		if($("#"+idTr).hasClass('onload'))
		{
			var currentClass = $("#"+idTr).attr('class');
			var currentUrl='<?php echo $currentUrlEmployee; ?>';
			currentUrl=currentUrl+'&ItMe='+id+'&classParent='+currentClass;
			//currentUrl=currentUrl+'&ItMe='+id;
			//showLoader(idTr);
			$('.p-'+idTr).remove();
			jQuery.ajax({
				url: currentUrl,
				type: "GET",
				cache: false,
				success: function (html) {
					var left='';
					var leftManager=''
					var right='';
					var rightManager='';

					var arrHtml=JSON.parse(html);
					//$sessionGraph=arrHtml.graph;
					//$sessionGraphMonth=arrHtml.graphMonth;
					$.each(arrHtml, function(key,val){
						if(key=='session')
						{
							$sessionGraph=val.graph;
							$sessionGraphMonth=val.graphMonth;
						}
						if(typeof val.left !='undefined' )
						{
							left+=val.left;
							right+=val.right;
						}

						if(typeof val.leftManager !='undefined' )
						{
							leftManager+=val.leftManager;
							rightManager+=val.rightManager;
						}
					});

					$('<div class=\'box-left\' rel="'+idTr+'" id="div-box-left-'+idTr+'"><table id="box-left-'+idTr+'">'+leftActi+leftManager+leftPC+left+'</table></div>').insertAfter( "#"+idTr );
					$('<div class=\'box-right\' rel="'+idTr+'" id="div-box-right-'+idTr+'"><table id="box-right-'+idTr+'">'+rightActi+rightManager+rightPC+right+'</table></div>').insertAfter( "#r-"+idTr );
					$('.showLoader').remove();

					$( "#"+idTr ).removeClass('onload');
					$( "#"+idTr ).removeClass('acti');
                    //fill pc
                    //get ids
                    var idList = [];
                    $('table[rel^="list-pc"]').each(function(){
                        var id = parseInt($(this).attr('rel').replace(/[^0-9]/g, ''));
                        idList.push(id);
                    });
                    $('#loading').css('display', 'inline');

                    is_resource = 0;
                    type = 1;
                    getStaffing(idList, function(){
                        is_resource = 1;
                        type = 0;
                        resource = [];
                        $('table[rel^="list-employee"]').each(function(){
                            var id = parseInt($(this).attr('rel').replace(/[^0-9]/g, ''));
                            resource.push(id);
                        });
                        getStaffing(resource, function(){
                            $('#loading').hide();
                        });
                    });
                    //fill employee

					hideChildrenItem();
				}
			});
		}
		else
		{
			if($("#"+idTr).hasClass('acti'))
			{
				$('#'+idTr).removeClass('acti');
				$('.pp-'+idTr).fadeIn();
				$('.trFamily').hide();
				$('.trFamily .gantt-capacity').addClass('displayNone');
				$('.trFamily .gantt-employee').addClass('displayNone');
				$('.trSubFamily .gantt-capacity').addClass('displayNone');
				$('.trSubFamily .gantt-employee').addClass('displayNone');
				$('.trActivity .gantt-capacity').addClass('displayNone');
				$('.trActivity .gantt-employee').addClass('displayNone');
				//$('.p-'+idTr).fadeIn();
			}
			else
			{
				$('.pp-'+idTr).addClass('acti');
				$('.p-'+idTr).addClass('acti');
				$('#'+idTr).addClass('acti');
				$('.p-'+idTr).fadeOut();
			}
		}
		setScrollBarWhenScroll();
		/*$('#dialog_staffing_screen').dialog({
            position    :'center',
            autoOpen    : true,
            autoHeight  : true,
            modal       : true,
            width       : 300,
            open : function(e){
                //var $dialog = $(e.target);
            }
        });*/
	}
	<?php
	}
	else
	{ ?>
	function ajaxShowEmployee(idTr,id){
		if($("#"+idTr).hasClass('onload'))
		{
			var currentClass = $("#"+idTr).attr('class');
			var currentUrl='<?php echo $currentUrlEmployee; ?>';
			currentUrl=currentUrl+'&ItMe='+id+'&classParent='+currentClass;
			$('.p-'+idTr).remove();
			jQuery.ajax({
				url: currentUrl,
				type: "GET",
				cache: false,
				success: function (html) {
					var left='';
					var right='';
					var arrHtml=JSON.parse(html);
					$.each(arrHtml, function(key,val){
						left+=val.left;
						right+=val.right;
					});
					$(left).insertAfter( "#"+idTr );
					$(right).insertAfter( "#r-"+idTr );
					$('.showLoader').remove();
					$( "#"+idTr ).removeClass('onload');
					$( "#"+idTr ).removeClass('acti');
					$('.trFamily').hide();
					$('.trFamily .gantt-capacity').addClass('displayNone');
					$('.trFamily .gantt-employee').addClass('displayNone');
					$('.trSubFamily .gantt-capacity').addClass('displayNone');
					$('.trSubFamily .gantt-employee').addClass('displayNone');
					$('.trActivity .gantt-capacity').addClass('displayNone');
					$('.trActivity .gantt-employee').addClass('displayNone');
					//$('.check-name').removeAttr('rowspan');
					$('.trEmployee .check-name').attr('rowspan', 6);
					$('.trFamily .check-name').attr('rowspan', rowspanOne);
					$('.trSubFamily .check-name').attr('rowspan', rowspanOne);
					$('.trActivity .check-name').attr('rowspan', rowspanOne);
				}
			});
		}
		else
		{
			if($("#"+idTr).hasClass('acti'))
			{
				$('#'+idTr).removeClass('acti');
				$('.pp-'+idTr).fadeIn();
				$('.trFamily').hide();
				$('.trFamily .gantt-capacity').addClass('displayNone');
				$('.trFamily .gantt-employee').addClass('displayNone');
				$('.trSubFamily .gantt-capacity').addClass('displayNone');
				$('.trSubFamily .gantt-employee').addClass('displayNone');
				$('.trActivity .gantt-capacity').addClass('displayNone');
				$('.trActivity .gantt-employee').addClass('displayNone');
				//$('.p-'+idTr).fadeIn();
			}
			else
			{
				$('.pp-'+idTr).addClass('acti');
				$('.p-'+idTr).addClass('acti');
				$('#'+idTr).addClass('acti');
				$('.p-'+idTr).fadeOut();
			}
		}
	}
	<?php
	} ?>
	function hideChildrenItem(){
		$('.trFamily').hide();
		$('.trFamily .gantt-capacity').addClass('displayNone');
		$('.trFamily .gantt-employee').addClass('displayNone');
		$('.trSubFamily .gantt-capacity').addClass('displayNone');
		$('.trSubFamily .gantt-employee').addClass('displayNone');
		$('.trActivity .gantt-capacity').addClass('displayNone');
		$('.trActivity .gantt-employee').addClass('displayNone');
		//$('.check-name').removeAttr('rowspan');
		//$('.trEmployee .check-name').attr('rowspan', <?php echo $colCapacityByYears ?>);
		$('.trFamily .check-name').attr('rowspan', rowspanOne);
		$('.trSubFamily .check-name').attr('rowspan', rowspanOne);
		$('.trActivity .check-name').attr('rowspan', rowspanOne);
	}
	function ajaxShowEmployeeBak(idTr,id){
		if($("#"+idTr).hasClass('onload'))
		{
			var currentClass = $("#"+idTr).attr('class');
			var currentUrl='<?php echo $currentUrlEmployee; ?>';
			currentUrl=currentUrl+'&ItMe='+id+'&classParent='+currentClass;
			//currentUrl=currentUrl+'&ItMe='+id;
			//showLoader(idTr);
			$('.p-'+idTr).remove();
			jQuery.ajax({
				url: currentUrl,
				type: "GET",
				cache: false,
				success: function (html) {
					var left='';
					var right='';
					var arrHtml=JSON.parse(html);
					$.each(arrHtml, function(key,val){
						left+=val.left;
						right+=val.right;
					});
					$(left).insertAfter( "#"+idTr );
					$(right).insertAfter( "#r-"+idTr );
					$('.showLoader').remove();
					$( "#"+idTr ).removeClass('onload');
					$( "#"+idTr ).removeClass('acti');
					//$('.p-'+idTr).fadeOut();
					//$('.pp-'+idTr).fadeIn();
					$('.trFamily').hide();
					$('.trFamily .gantt-capacity').addClass('displayNone');
					$('.trFamily .gantt-employee').addClass('displayNone');
					$('.trSubFamily .gantt-capacity').addClass('displayNone');
					$('.trSubFamily .gantt-employee').addClass('displayNone');
					$('.trActivity .gantt-capacity').addClass('displayNone');
					$('.trActivity .gantt-employee').addClass('displayNone');
					//$('.check-name').removeAttr('rowspan');
					$('.trEmployee .check-name').attr('rowspan', 6);
					$('.trFamily .check-name').attr('rowspan', rowspanOne);
					$('.trSubFamily .check-name').attr('rowspan', rowspanOne);
					$('.trActivity .check-name').attr('rowspan', rowspanOne);

					//$('.gantt-summary').find('.check-name').attr('rowspan', 6);
					//$('.gantt-summary').find('.gantt-capacity').removeClass('displayNone');
					//$('.gantt-summary').find('.gantt-employee').removeClass('displayNone');
				}
			});
		}
		else
		{
			if($("#"+idTr).hasClass('acti'))
			{
				$('#'+idTr).removeClass('acti');
				$('.pp-'+idTr).fadeIn();
				$('.trFamily').hide();
				$('.trFamily .gantt-capacity').addClass('displayNone');
				$('.trFamily .gantt-employee').addClass('displayNone');
				$('.trSubFamily .gantt-capacity').addClass('displayNone');
				$('.trSubFamily .gantt-employee').addClass('displayNone');
				$('.trActivity .gantt-capacity').addClass('displayNone');
				$('.trActivity .gantt-employee').addClass('displayNone');
				//$('.p-'+idTr).fadeIn();
			}
			else
			{
				$('.pp-'+idTr).addClass('acti');
				$('.p-'+idTr).addClass('acti');
				$('#'+idTr).addClass('acti');
				$('.p-'+idTr).fadeOut();
			}
		}
	}
    function SubmitDataExport(){
        var $ = jQuery;
        $('#dialog_vision_export').dialog({
            position    :'center',
            autoOpen    : true,
            autoHeight  : true,
            modal       : true,
            width       : 300,
            open : function(e){
                //var $dialog = $(e.target);
            }
        });
        $('#no_port').click(function(){
            $('#dialog_vision_export').dialog('close');
        });
        $('#ok_port').click(function(){
            var input = $('#dialog_vision_export input[name="displayFields[]"]').filter(':checked');
            if(!input.length){
                alert('<?php echo h(__('Please choose a fields to export' , true)); ?>');
            }else{
                $('#dialog_vision_export').dialog('close');
                //var rs = [];
                //$.each(input);
                $('#ExportDisplayFields').val($.map(input.get() , function(v){return $(v).val();}).join(','));
                $('#GanttChartDIV').html2canvas();
            }
        });
        $('#DisplayFields0').click(function(){
            $(this).closest('.input').find('input').prop('checked' , $(this).is(':checked'));
        });
    }
    var isCheck = <?php echo json_encode($isCheck);?>;
    var showType = <?php echo json_encode($showType);?>;
    (function($){

        //createDialog();


        var na = '<?php echo $this->GanttVs->na; ?>';
        /**
         * Convert to float
         *
         * @param val
         * @param dp
         *
         * return mixed na or float number
         */
        var toFloat = function (val,dp){
            if(dp && (val.length == 0 || val == na)){
                return na;
            }
            //val =  Number(parseFloat(val || '0').toFixed(1));
            return (isNaN(val) || val <= 0)  ? 0 : val;
        }
        /**
         * Attach grid element event
         *
         */
        GanttCallback = function($list, $gantt){
            //console.trace();
            $gantt.find('.gantt-input').each(function(){
                var  $element =  $(this);
                var val = toFloat($element.html() , true);
                //$element.html(val);
                if(val != na && val > 0){
                    $element.addClass('gantt-unzero');
                }
            });

            if((showType == 0 || showType == 5) && (isCheck == 2 || isCheck == 0)){
                $('.gantt-capacity').addClass('displayNone');
                $('.gantt-employee').addClass('displayNone');
                $('.check-name').removeAttr('rowspan');
                $('.check-name').attr('rowspan', rowspanOne);

                $('.gantt-summary').find('.check-name').attr('rowspan', <?php echo $colCapacityByYears;?>);
                $('.gantt-summary').find('.gantt-capacity').removeClass('displayNone');
                $('.gantt-summary').find('.gantt-employee').removeClass('displayNone');
            }
            if((showType == 0 || showType == 5) && isCheck == 1){
                $('.gantt-capacity').addClass('displayNone');
                $('.check-name').removeAttr('rowspan');
                $('.check-name').attr('rowspan', rowspanOne);
                $('.gantt-summary').find('.check-name').attr('rowspan', <?php echo $colCapacityByYears;?>);
                $('.gantt-summary').find('.gantt-capacity').removeClass('displayNone');

				$('.gantt-employee').find('.check-name').attr('rowspan', <?php echo $colCapacityByYears;?>);
                $('.gantt-employee').find('.gantt-capacity').removeClass('displayNone');
            }
            /*if(showType == 5 && isCheck == false){
                $('.gantt-capacity').addClass('displayNone');
                $('.check-name').removeAttr('rowspan');
                $('.check-name').attr('rowspan', 3);

                $('.gantt-summary').find('.check-name').attr('rowspan', 9);
                $('.gantt-summary').find('.gantt-capacity').removeClass('displayNone');
            }*/
        }
        var mgLeft = $('#mcs_container').find('.container').width();
        $('.gantt-chart-0').css('margin-left', mgLeft-3);
    })(jQuery);
	function toggleEmployee(id)
	{
		//$('.'+id).removeClass('acti');
		if($('#tr-'+id).hasClass('acti'))
		{
			$('#tr-'+id).removeClass('acti');
			$('.trFamily.'+id).fadeIn();
		}
		else
		{
			$('#tr-'+id).addClass('acti');
			//$('.trSubFamily.'+id).fadeOut();
			$('.trFamily.'+id).addClass('acti');
			$('.trSubFamily.'+id).addClass('acti');
			$('.'+id).fadeOut();
		}
		<?php
		if($showType==1)
		{ ?>
		setScrollBarWhenScroll();
		<?php }?>
	}
	function toggleSub(id)
	{
		//$('.'+id).removeClass('acti');
		if($('#tr-'+id).hasClass('acti'))
		{
			$('#tr-'+id).removeClass('acti');
			$('.trSubFamily.'+id).fadeIn();
		}
		else
		{
			$('#tr-'+id).addClass('acti');
			//$('.trSubFamily.'+id).fadeOut();
			$('.trSubFamily.'+id).addClass('acti');
			$('.'+id).fadeOut();
		}
		<?php
		if($showType==1)
		{ ?>
		setScrollBarWhenScroll();
		<?php }
		elseif($showType==0){
			if($activityType==0){
				?>
				hideShowBoxResource(id,true);
				<?php
			}
		}?>
	}
	function toggleActivity(id)
	{
		if($('#tr-'+id).hasClass('acti'))
		{
			$('#tr-'+id).removeClass('acti');
			$('.trActivity.'+id).fadeIn();
		}
		else
		{
			$('#tr-'+id).addClass('acti');
			$('.trActivity.'+id).fadeOut();
		}

		<?php
		if($showType==1)
		{ ?>
		setScrollBarWhenScroll();
		<?php }
		elseif($showType==0){
			if($activityType==0){
				?>
				hideShowBoxResource(id,true);
				<?php
			}
		}?>

	}
	function toggleGeneral(id)
	{
		if($('#tr-'+id).hasClass('acti'))
		{
			$('#tr-'+id).removeClass('acti');
			$('.'+id).fadeIn();
		}
		else
		{
			$('#tr-'+id).addClass('acti');
			$('.'+id).fadeOut();
		}
	}
    function getStaffing(idList, callback){
        var url = '<?php echo $this->Html->url('/new_staffing/') ?>?start_date=' + start + '&end_date=' + end + '&view_by=' + viewby + '&type=' + type + '&summary=' + showSummary + '&family=' + family + '&subfamily=' + subfamily + '&activity=' + activity + '&customer=' + customer + '&pc=' + pc + '&priority=' + priority;
        if( type == 1 )url += '&list=' + JSON.stringify(idList);
        if( onlysummary )url += '&only_sum=1';
        if( is_resource ){
			var listResource = [];
			$('.trEmployee[id^="tr-employee"]').each(function(){
				listResource.push($(this).prop('id').replace('tr-employee-', ''));
			});
			url += '&is_resource=1';
			url += '&resource=' + listResource.join(',');
		}
        $.getJSON(url, function(data){
            var t = '[rel="pc-%s"]',
                st = '[rel="list-pc-%s"]';
            if( is_resource ){
                t = '[rel="employee-%s"]';
                st = '[rel="list-employee-%s"]';
            }
            fill2(idList, data, t ,st);
            if( callback )callback.call(this);
            exporterReady = true;
        });
    }
    $(document).ready(function(){
        if( !is_resource && showSummary && type == 0 ){
            GanttDone();
        }
    });
    function fill2(idList, data, t, st){
        if( showSummary )idList.push('summary');
        $.each(idList, function(sdf, id){
            if( data[id] ){
                var sum = {};
                var table = $(t.replace('%s', id)),
                    tableYear = $(st.replace('%s', id));
                if( id == 'summary' ){
                    table = $('[rel="summary"]');
                    tableYear = $('[rel="list-summary"]');
                }
                $.each(data.list, function(time){
                    //if( !table.find('.cell-consumed-' + time + ' div').length )return;
                    var date = new Date(time * 1000), year = date.getFullYear();
                    if( !sum[year] ){
                        sum[year] = {
                            absence: 0,
                            capacity: 0,
                            working: 0,
                            capacity_theoretical: 0,
                            notValidated: 0
                        };
                    }
                    //get workload
                    var x = data[id];
                    var workload = parseFloat(table.find('.cell-workload-' + time).text());
                    //absence
                    if( x.absence && x.absence[time] ){
                        value = parseFloat(x.absence[time]);
                        var cell = table.find('.cell-absence-' + time + ' div');
                        cell.text(value.toFixed(2));
                        //sum section
                        sum[year].absence += value;
                    }
                    //resource
                    if( x.resource && x.resource[time] ){
                        value = parseFloat(x.resource[time]);
                        var cell = table.find('.cell-employee-' + time + ' div');
                        cell.text(value);
                    }
                    if( x.capacity_theoretical && x.capacity_theoretical[time] ){
                        value = parseFloat(x.capacity_theoretical[time]);
                        var cell = table.find('.cell-capacity_theoretical-' + time + ' div');
                        if( cell.length ){
                            cell.parent().removeClass('gantt-invalid gantt-green');
                            if( value.capacity_theoretical < workload ){
                                cell.parent().addClass('gantt-invalid');
                            } else {
                                cell.parent().addClass('gantt-green');
                            }
                            cell.text(value.toFixed(2));
                            //sum section
                            sum[year].capacity_theoretical += value;
                        }
                    }
                    if( x.capacity && x.capacity[time] ){
                        //capacity
                        value = parseFloat(x.capacity[time]);
                        var cell = table.find('.cell-capacity-' + time + ' div');
                        cell.parent().removeClass('gantt-invalid gantt-green');
                        if( value < workload ){
                            cell.parent().addClass('gantt-invalid');
                        }
                        cell.text(value.toFixed(2));
                        //sum section
                        sum[year].capacity += value;
                        //days not validated = capacity - consumed
                        var cell = table.find('.cell-notValidated-' + time + ' div');
                        var nv = value - parseFloat(table.find('.cell-consumed-' + time).text());
                        cell.text(nv.toFixed(2));
                        //sum section
                        sum[year].notValidated += nv;
                    } else {
                        table.find('.cell-capacity-' + time).removeClass('gantt-invalid gantt-green');
                    }
                    //calculate fte here
                    if( x.working && x.working[time] ){
                        value = parseFloat(x.working[time]);
                        var cell = table.find('.cell-working-' + time + ' div');
                        cell.text(value);
                        //sum section
                        sum[year].working += value;
                        //dont sum fte
                        if( value > 0 && viewby == 'month' ){
                            //real fte
                            cell = table.find('.cell-realfte-' + time);
                            div = cell.find('div');
                            cell.removeClass('gantt-invalid gantt-green');
                            //real fte = (workload - capacity) / workday
                            var v = parseFloat(table.find('.cell-workload-' + time).text()) - (x.capacity[time] ? x.capacity[time] : 0);
                            v = v/value;
                            if( v <= 0 ){
                                cell.addClass('gantt-green');
                            } else {
                                cell.addClass('gantt-invalid');
                            }
                            div.text(v.toFixed(2));
                            //theo fte
                            cell = table.find('.cell-fte-' + time);
                            cell.removeClass('gantt-invalid gantt-green');
                            div = cell.find('div');
                            //real fte = (workload - theocapacity) / workday
                            v = parseFloat(table.find('.cell-workload-' + time).text()) - (x.capacity_theoretical[time] ? x.capacity_theoretical[time] : 0);
                            v = v/value;
                            if( v <= 0 ){
                                cell.addClass('gantt-green');
                            } else {
                                cell.addClass('gantt-invalid');
                            }
                            div.text(v.toFixed(2));
                        }
                    }
                });
                var sumY = {
                    absence: 0,
                    capacity: 0,
                    working: 0,
                    capacity_theoretical: 0,
                    notValidated: 0
                };
                //now calculate sum each item in a year
                $.each(sum, function(year, value){
                    var workload = parseFloat(tableYear.find('.total-workload [rel="total-' + year + '"]').eq(0).text());
                    if( typeof value.capacity != 'undefined' ){
                        var cell = tableYear.find('.total-capacity [rel="total-' + year + '"]').eq(0),
                            div = cell.find('div');
                        cell.removeClass('gantt-invalid gantt-green');
                        if( value.capacity < workload ){
                            cell.addClass('gantt-invalid');
                        }
                        div.text(value.capacity.toFixed(2));
                        sumY.capacity += value.capacity;
                    }
                    if( typeof value.absence != 'undefined' ){
                        var cell = tableYear.find('.total-absence [rel="total-' + year + '"]').eq(0),
                            div = cell.find('div');
                        div.text(value.absence.toFixed(2));
                        sumY.absence += value.absence;
                    }
                    if( typeof value.working != 'undefined' ){
                        var cell = tableYear.find('.total-working [rel="total-' + year + '"]').eq(0),
                            div = cell.find('div');
                        div.text(value.working.toFixed(2));
                        sumY.working += value.working;
                    }
                    if( typeof value.notValidated != 'undefined' ){
                        var cell = tableYear.find('.total-notValidated [rel="total-' + year + '"]').eq(0),
                            div = cell.find('div');
                        div.text(value.notValidated.toFixed(2));
                        sumY.notValidated += value.notValidated;
                    }
                    if( typeof value.capacity_theoretical != 'undefined' ){
                        var cell = tableYear.find('.total-capacity_theoretical [rel="total-' + year + '"]').eq(0),
                            div = cell.find('div');
                        cell.removeClass('gantt-invalid gantt-green');
                        if( value.capacity_theoretical < workload ){
                            cell.addClass('gantt-invalid');
                        } else {
                            cell.addClass('gantt-green');
                        }
                        div.text(value.capacity_theoretical.toFixed(2));
                        sumY.capacity_theoretical += value.capacity_theoretical;
                    }
                    if( value.working > 0 && viewby == 'month' ){
                        fte = ( workload - value.capacity ) / value.working;
                        var cell = tableYear.find('.total-realfte [rel="total-' + year + '"]').eq(0),
                            div = cell.find('div');
                        cell.removeClass('gantt-invalid gantt-green');
                        if( fte <= 0 ){
                            cell.addClass('gantt-green');
                        } else {
                            cell.addClass('gantt-invalid');
                        }
                        div.html(fte.toFixed(2));
                        //theo fte
                        theofte = ( workload - value.capacity_theoretical ) / value.working;
                        cell = tableYear.find('.total-fte [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                        cell.removeClass('gantt-invalid gantt-green');
                        if( theofte <= 0 ){
                            cell.addClass('gantt-green');
                        } else {
                            cell.addClass('gantt-invalid');
                        }
                        div.html(theofte.toFixed(2));
                    }
                });
                // sum all years here
                var workload = parseFloat(tableYear.find('.total-workload [rel="all-total"]').eq(0).text());
                if( typeof sumY.capacity != 'undefined' ){
                    var cell = tableYear.find('.total-capacity td.percent').prev(),
                        div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( sumY.capacity < workload ){
                        cell.addClass('gantt-invalid');
                    }
                    div.text(sumY.capacity.toFixed(2));
                }
                if( typeof sumY.absence != 'undefined' ){
                    var cell = tableYear.find('.total-absence td.percent').prev(),
                        div = cell.find('div');
                    div.text(sumY.absence.toFixed(2));
                }
                if( typeof sumY.working != 'undefined' ){
                    var cell = tableYear.find('.total-working td.percent').prev(),
                        div = cell.find('div');
                    div.text(sumY.working.toFixed(2));
                }
                if( typeof sumY.notValidated != 'undefined' ){
                    var cell = tableYear.find('.total-notValidated td.percent').prev(),
                        div = cell.find('div');
                    div.text(sumY.notValidated.toFixed(2));
                }
                if( viewby == 'month' ){
                    if( typeof sumY.capacity_theoretical != 'undefined' ){
                        var cell = tableYear.find('.total-capacity_theoretical td.percent').prev(),
                            div = cell.find('div');
                        cell.removeClass('gantt-invalid gantt-green');
                        if( sumY.capacity_theoretical < workload ){
                            cell.addClass('gantt-invalid');
                        } else {
                            cell.addClass('gantt-green');
                        }
                        div.text(sumY.capacity_theoretical.toFixed(2));
                    }
                    //
                    var realfte = 0, fte = 0;
                    tableYear.find('.total-realfte .wd-work-year:not(:last)').each(function(){
                        realfte += parseFloat($(this).text());
                    });
                    tableYear.find('.total-fte .wd-work-year:not(:last)').each(function(){
                        fte += parseFloat($(this).text());
                    });
                    var cell = tableYear.find('.total-fte td.percent').prev(),
                    cellreal = tableYear.find('.total-realfte td.percent').prev();
                    cell.removeClass('gantt-invalid gantt-green');
                    cellreal.removeClass('gantt-invalid gantt-green');
                    if( fte <= 0 )cell.addClass('gantt-green');
                    else cell.addClass('gantt-invalid');
                    if( realfte <= 0 )cellreal.addClass('gantt-green');
                    else cellreal.addClass('gantt-invalid');
                    cell.find('div').text(fte.toFixed(2));
                    cellreal.find('div').text(realfte.toFixed(2));
                }
            }
        });
    }
    function GanttDone(){
        if( loadOnce )return;
        loadOnce = true;
        $('#loading').css('display', 'inline');
        $('table[rel^="list-pc"],table[rel^="list-employee"]').each(function(){
            var id = parseInt($(this).attr('rel').replace(/[^0-9]/g, ''));
            ids.push(id);
        });
        getStaffing(ids, function(){
            $('#loading').hide();
        });
    }
    function fill(url, table, tableYear){
        $('#loading').css('display', 'inline');
        $.getJSON(url, function(data) {
            buildS = false;
            fillTable(data, table, tableYear);
            //build summary
            // if( buildS ){
            //     buildS = false;
            //     fillTable({
            //         summary: summary
            //     }, table, tableYear);
            // }
            $('#loading').hide();
        });
    }
    function s(time, key, val){
        if( !buildS )return;
        if( !summary[time] )summary[time] = {};
        //dont sum working day
        if( !summary[time][key] || key == 'working' )summary[time][key] = val;
        else summary[time][key] += val;
    }
    function fillTable(d, t, ty){
        $.each(d, function(id, data){
            var sum = {},
                sumY = {},
                noParent = buildS && !hasParent(id);
            if( isCheck && id != 'summary' ){
                ty = '[rel="list-employee-%s"]';
                t = '[rel="employee-%s"]';
            }
            tableYear = $(ty.replace('%s', id));
            table = $(t.replace('%s', id));
            //iterate each time
            $.each(data, function(time, value){
                var date = new Date(time * 1000),
                    year = date.getFullYear();
                if( !(startDate <= time && time <= endDate) ){
                    return;
                }
                $.each(value, function(i, v){
                    value[i] = parseFloat(v);
                });
                if( !sum[year] ){
                    sum[year] = {};
                }
                //manually add it here
                var workload = parseFloat(table.find('.cell-workload-' + time).text());
                if( typeof value.capacity != 'undefined' ){
                    var cell = table.find('.cell-capacity-' + time),
                        div = cell.find('div');
                    div.html(value.capacity.toFixed(2));
                    cell.removeClass('gantt-invalid gantt-green');
                    if( value.capacity < workload ){
                        cell.addClass('gantt-invalid');
                    }
                    if( !sum[year].capacity ){
                        sum[year].capacity = 0;
                    }
                    sum[year].capacity += value.capacity;
                    if( noParent )s(time, 'capacity', value.capacity);
                }
                if( typeof value.absence != 'undefined' ){
                    var div = table.find('.cell-absence-' + time + ' div');
                    div.html(value.absence.toFixed(2));
                    if( !sum[year].absence ){
                        sum[year].absence = 0;
                    }
                    sum[year].absence += value.absence;
                    if( noParent )s(time, 'absence', value.absence);
                }
                if( typeof value.working != 'undefined' ){
                    var div = table.find('.cell-working-' + time + ' div');
                    div.html(value.working.toFixed(2));
                    if( !sum[year].working ){
                        sum[year].working = 0;
                    }
                    sum[year].working += value.working;
                    if( noParent )
                        s(time, 'working', value.working);
                }
                if( typeof value.capacity_theoretical != 'undefined' ){
                    var cell = table.find('.cell-capacity_theoretical-' + time),
                        div = cell.find('div');
                    div.html(value.capacity_theoretical.toFixed(2));
                    cell.removeClass('gantt-invalid gantt-green');
                    if( value.capacity_theoretical < workload ){
                        cell.addClass('gantt-invalid');
                    } else {
                        cell.addClass('gantt-green');
                    }
                    if( !sum[year].capacity_theoretical ){
                        sum[year].capacity_theoretical = 0;
                    }
                    sum[year].capacity_theoretical += value.capacity_theoretical;
                    if( noParent )
                        s(time, 'capacity_theoretical', value.capacity_theoretical);
                }
                if( typeof value.notValidated != 'undefined' ){
                    var div = table.find('.cell-notValidated-' + time + ' div');
                    div.html(value.notValidated.toFixed(2));
                    if( !sum[year].notValidated ){
                        sum[year].notValidated = 0;
                    }
                    sum[year].notValidated += value.notValidated;
                    if( noParent )s(time, 'notValidated', value.notValidated);
                }
                if( typeof value.totalEmployee != 'undefined' || typeof value.employee != 'undefined' ){
                    val = value.totalEmployee || value.employee;
                    if( !val )val = 0;
                    var div = table.find('.cell-employee-' + time + ' div');
                    div.html(val);
                    if( noParent )s(time, 'employee', val);
                }
                //things like fte will be calculated differently
                //fte = ( workload - capacity ) / workingDays
                if( typeof value.working != 'undefined' ){
                    fte = ( workload - value.capacity ) / value.working;
                    var cell = table.find('.cell-realfte-' + time),
                        div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( fte <= 0 ){
                        cell.addClass('gantt-green');
                    } else {
                        cell.addClass('gantt-invalid');
                    }
                    div.html(fte.toFixed(2));
                    //theo fte
                    theofte = ( workload - value.capacity_theoretical ) / value.working;
                    cell = table.find('.cell-fte-' + time);
                    div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( theofte <= 0 ){
                        cell.addClass('gantt-green');
                    } else {
                        cell.addClass('gantt-invalid');
                    }
                    div.html(theofte.toFixed(2));
                }
            });
            //now calculate sum each item in a year
            $.each(sum, function(year, value){
                var workload = parseFloat(tableYear.find('.total-workload [rel="total-' + year + '"]').eq(0).text());
                if( typeof value.capacity != 'undefined' ){
                    var cell = tableYear.find('.total-capacity [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( value.capacity < workload ){
                        cell.addClass('gantt-invalid');
                    }
                    div.text(value.capacity.toFixed(2));
                    if( !sumY.capacity ){
                        sumY.capacity = 0;
                    }
                    sumY.capacity += value.capacity;
                }
                if( typeof value.absence != 'undefined' ){
                    var cell = tableYear.find('.total-absence [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                    div.text(value.absence.toFixed(2));
                    if( !sumY.absence ){
                        sumY.absence = 0;
                    }
                    sumY.absence += value.absence;
                }
                if( typeof value.working != 'undefined' ){
                    var cell = tableYear.find('.total-working [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                    div.text(value.working.toFixed(2));
                    if( !sumY.working ){
                        sumY.working = 0;
                    }
                    sumY.working += value.working;
                }
                if( typeof value.notValidated != 'undefined' ){
                    var cell = tableYear.find('.total-notValidated [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                    div.text(value.notValidated.toFixed(2));
                    if( !sumY.notValidated ){
                        sumY.notValidated = 0;
                    }
                    sumY.notValidated += value.notValidated;
                }
                if( typeof value.capacity_theoretical != 'undefined' ){
                    var cell = tableYear.find('.total-capacity_theoretical [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( value.capacity_theoretical < workload ){
                        cell.addClass('gantt-invalid');
                    } else {
                        cell.addClass('gantt-green');
                    }
                    div.text(value.capacity_theoretical.toFixed(2));
                    if( !sumY.capacity_theoretical ){
                        sumY.capacity_theoretical = 0;
                    }
                    sumY.capacity_theoretical += value.capacity_theoretical;
                }
                if( typeof value.working != 'undefined' ){
                    fte = ( workload - value.capacity ) / value.working;
                    var cell = tableYear.find('.total-realfte [rel="total-' + year + '"]').eq(0),
                        div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( fte <= 0 ){
                        cell.addClass('gantt-green');
                    } else {
                        cell.addClass('gantt-invalid');
                    }
                    div.html(fte.toFixed(2));
                    //theo fte
                    theofte = ( workload - value.capacity_theoretical ) / value.working;
                    cell = tableYear.find('.total-fte [rel="total-' + year + '"]').eq(0),
                    div = cell.find('div');
                    cell.removeClass('gantt-invalid gantt-green');
                    if( theofte <= 0 ){
                        cell.addClass('gantt-green');
                    } else {
                        cell.addClass('gantt-invalid');
                    }
                    div.html(theofte.toFixed(2));
                }
            });
            // sum all years here
            var workload = parseFloat(tableYear.find('.total-workload [rel="all-total"]').eq(0).text());
            if( typeof sumY.capacity != 'undefined' ){
                var cell = tableYear.find('.total-capacity td.percent').prev(),
                    div = cell.find('div');
                cell.removeClass('gantt-invalid gantt-green');
                if( sumY.capacity < workload ){
                    cell.addClass('gantt-invalid');
                }
                div.text(sumY.capacity.toFixed(2));
            }
            if( typeof sumY.absence != 'undefined' ){
                var cell = tableYear.find('.total-absence td.percent').prev(),
                    div = cell.find('div');
                div.text(sumY.absence.toFixed(2));
            }
            if( typeof sumY.working != 'undefined' ){
                var cell = tableYear.find('.total-working td.percent').prev(),
                    div = cell.find('div');
                div.text(sumY.working.toFixed(2));
            }
            if( typeof sumY.notValidated != 'undefined' ){
                var cell = tableYear.find('.total-notValidated td.percent').prev(),
                    div = cell.find('div');
                div.text(sumY.notValidated.toFixed(2));
            }
            if( typeof sumY.capacity_theoretical != 'undefined' ){
                var cell = tableYear.find('.total-capacity_theoretical td.percent').prev(),
                    div = cell.find('div');
                cell.removeClass('gantt-invalid gantt-green');
                if( sumY.capacity_theoretical < workload ){
                    cell.addClass('gantt-invalid');
                } else {
                    cell.addClass('gantt-green');
                }
                div.text(sumY.capacity_theoretical.toFixed(2));
            }
            //
            var realfte = 0, fte = 0;
            tableYear.find('.total-realfte .wd-work-year:not(:last)').each(function(){
                realfte += parseFloat($(this).text());
            });
            tableYear.find('.total-fte .wd-work-year:not(:last)').each(function(){
                fte += parseFloat($(this).text());
            });
            tableYear.find('.total-fte td.percent').prev().find('div').text(fte.toFixed(2));
            tableYear.find('.total-realfte td.percent').prev().find('div').text(realfte.toFixed(2));
        });
    }
    function hasParent(id){
        if( id == 'summary' )return;
        var tr = $('#tr-' + id),
            classes = [],
            result = false;;
        if( isCheck ){
            tr = $('#tr-employee-' + id);
        }
        classes = tr.prop('class').split(/\s+/);
        for(var i in classes){
            c = classes[i];
            if( c.substr(0, 4) == 'p-tr' ){
                pId = c.replace('p-tr-', '');
                if( $('#tr-' + pId).length ){
                    result = true;
                    break;
                }
            }
        }
        return result;
    }
    // function buildSummary(d){
    //     $.each(d, function(id){
    //         if( id == 'summary' )return;
    //         console.log(id, hasParent(id));
    //     });
    // }
</script>
<style>
    .displayNone{
        display: none;
    }
	.gantt-title.gantt-head td{
		background-color: #185790 !important;
	}
	.gantt-title.gantt-head td.gantt-name div{
		background:none !important;
		padding-left:0 !important;
	}
	.gantt-title.gantt-head td.gantt-name .showLoader{
		display:none !important;
	}
</style>
<script type="text/javascript">
    function Exporter(){
        this.init();
        return this;
    }

    Exporter.prototype.init = function(){
        this.result = {};
        this.header = {};
        var inst = this;
        //header: month
        var header = [];
        $('.gantt-head-scroll .gantt-staff .gantt-title td:not(:last)').each(function(){
            header.push($(this).text());
        });
        $('#export-header:first .gantt-num td').each(function(i){
            var text = $(this).text() + '-' + $('#export-header:first .gantt-head td').eq(i).text();
            header.push(text);
        });
        //extract data
        $('.export-left:not([rel^="list-employee-"])').each(function(index){
            var left = $(this),
                right = $('.export-table').eq(index),
                rows = [],
                title = '';
            //build left content
            left.find('tr:not(.gantt-head, .fixedHeightStaffing, :empty, .displayNone)').each(function(){
                var tr = $(this),
                    cols = [];
                if( tr.find('td').length == 1 ){
                    //single col, title
                    title = tr.find('td').text();
                } else {
                    //tranverse tds
                    tr.find('td:not(:last)').each(function(){
                        var isName = $(this).hasClass('gantt-func');
                        if( tr.hasClass('gantt-employee') && !isName ){
                            cols.push('');
                        } else {
                            if( isName ){
                                if( tr.hasClass('gantt-employee') ){
                                    isName = '[resource]' + $(this).text();
                                } else {
                                    isName = '[name]' + $(this).text();
                                }
                            } else {
                                isName = $(this).text();
                            }
                            cols.push(isName);
                        }
                    });
                    //push rows
                    rows.push(cols);
                }
            });
            //build right content
            right.find('tr:not(.gantt-head, .fixedHeightStaffing, :empty, .displayNone)').each(function(i){
                //traverse tds
                var row = rows[i];
                $(this).find('td').each(function(){
                    var td = $(this);
                    try {
                        row.push(td.text());
                    } catch (ex){

                    }
                });
            });
            
            //a fix to sort the key of object
            title = '[' + index + '] ' + title;
            inst.result[title] = rows;
        });
        inst.header = header;
    };

    // Exporter.prototype.commit = function(callback){
    //     callback.call(this, this._header, this._result);
    // };

    $('#export-submit').click(function(){
        if( !exporterReady ){
            alert('<?php __('Please wait!') ?>');
            return false;
        }
        var exporter = new Exporter();
        $('#export-data').val(JSON.stringify(exporter.result));
        $('#export-data-header').val(JSON.stringify(exporter.header));
        $('#overlay-container').show();
        $('#form-export').submit();
        setTimeout(function(){
            $('#overlay-container').hide();
        }, 5000);
        return false;
    });
</script>
<?php echo $this->Form->create(false, array('url' => '/new_staffing/export', 'id' => 'form-export', 'style'=> 'display: none')) ?>
<?php echo $this->Form->hidden('data', array('id' => 'export-data')) ?>
<?php echo $this->Form->hidden('header', array('id' => 'export-data-header')) ?>
<?php echo $this->Form->end() ?>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        <?php __('Please wait, Preparing export...') ?>
    </div>
</div>