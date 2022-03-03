<?php echo $html->script('jshashtable-2.1'); ?>
<?php echo $html->script('jquery.numberformatter-1.2.3'); ?>
<?php echo $html->script('jquery.formatCurrency-1.4.0'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->css('jquery.multiSelect'); ?> 
<?php //echo $html->script('jquery.dataTables'); ?>
<?php //echo $html->css('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>
<?php echo $html->css('dd'); ?>
<?php echo $html->script('jquery.dd'); ?>
<?php echo $html->css('gantt'); ?>

<?php 
	echo $this->Html->script(array(
		//'dashboard/jquery-1.10.1.min',
		'dashboard/jqx-all',
		'dashboard/jqxchart',
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
		'jquery.scrollTo'
	)); 
	echo $this->Html->css(array(
		'dashboard/jqx.base',
		'dashboard/jqx.web'
	));
function progressClass($percent){
	if( $percent < 60 ){
		echo 'progress-bar-danger';
	} else if( $percent < 80 ){
		echo 'progress-bar-warning';
	}
	else echo 'progress-bar-success';
}
?>
<style>
/*    body{color: #000;}textarea{}textarea:focus{ padding:10px; }*/
   .error-message{color:red;margin-left:35px;}.wd-weather-list ul li{padding-right:5px;}.gantt-chart-wrapper{overflow-x:auto;}.gantt-chart{margin-left:0!important;}#gantt-display div label{padding:0 6px;}#gantt-display{width:620px;}.inputcheckbox{float:left;width:50px;margin-left:-20px;margin-top:1px;}.group-content{border-top:1px solid #004381;margin-bottom:15px;margin-top:20px;}.group-content h3{float:left;padding-top:10px;background:url("<?php echo $this->Html->webroot('img/line-text2.png'); ?>");width:165px;height:26px;text-align:center;}.group-content h3 span{color:#FFF;font-weight:400;font-size:13px;}.group-content fieldset{float:left;margin-left:5px;}.group-content fieldset div.wd-submit{padding:2px 0!important;}.group-content fieldset div.wd-submit a.wd-reset{margin-left:5px!important;}.wd-t2-plus{margin-bottom:5px;font-size:20px;}.selection-plus{float:left;border:1px solid #d4d4d4;padding:6px;}fieldset div.wd-input{width:90%!important;}#table-cost table tr td{border:1px solid #d4d4d4;text-align:center;padding:5px;}#table-cost table tr td.cost-header{background-color:#376091;color:#FFF;}#table-cost table tr td.cost-md{background-color:#75923C;color:#FFF;}#table-cost table tr td.cost-euro{background-color:#95B3D7;color:#FFF;}.cost-disabled{background-color:#F5F5F5;}.checkbox,.wd-weather-list ul li input,.wd-weather-list ul li img,.wd-weather-list-dd ul li input,.wd-weather-list-dd ul li img{float:left;} .highcharts-container{ border:1px solid #999 !important;}.budget_external_chart{ margin-bottom:30px;}.full-width{width:26px; height:26px; margin:0 2px}.des{margin:4px 0px 15px 44px; display:block;}
	  .delay-plan{font-weight: bold;color:#000;}
	   .gantt-wrapper{}
	   #GanttChartDIV{width: 100%;}
	   .wd-weather-list-dd ul li{width: 80px;}.demo-gauge{float: left; margin-left:165px; width:240px !important; margin-top: 10px;}.num-progress{ text-align:center; margin-top:-20px;}
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
	/*border-bottom: 1px solid #999;*/
}
.pch_log_description textarea {

}
.has-scroll {
	max-height: 150px;
	overflow-y: scroll;
}
.pch_log {
	/*min-height: 100px;
	border: 1px solid #E0E0E0;
	border-top: 0;*/
	position: relative;
}
.pch_log_system_content {
	/*margin-top: 33px;*/
}
.pch_log_avatar_content {
	background: #fff;
	border: none !important;
}
.wd-title a.wd-add-project {
	padding-left: 26px;
}
.input_disabled{
	border:none;
	width:80%;
	/*padding-top:5px;
	font-size:18px;*/
}
	.kpi-visible-0 {
		display: none;
	}
</style>
<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
?>
<section class="content">  
	<div class="row">
		<div class="col-md-12">
		<?php echo $this->Session->flash(); ?>
			<div class="box box-primary">
				<div class="box-header">
					<h2 class="box-title"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']) ?></h2>
					<div class="box-tools pull-right"><?php echo $this->element("checkStaffingBuilding") ?></div>
				</div>
				<div class="box-body">
					<?php
					echo $this->Form->create('ProjectAmr', array('url' => array(
							'controller' => 'project_amrs', 'action' => 'index_plus', $projectName['Project']['id']
							)));
					echo $this->Form->input('project_id', array('type' => 'hidden', 'name' => 'data[ProjectAmr][project_id]', 'value' => $project_id));
					echo $this->Form->input('id', array('type' => 'hidden', 'name' => 'data[ProjectAmr][id]', 'value' => (@$this->data['ProjectAmr']['id']) ? $this->data['ProjectAmr']['id'] : ""));
					App::import("vendor", "str_utility");
					$str_utility = new str_utility();
					?>

					<button id="btnSave" class="btn btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> <?php __('Save') ?></button>
					<a href="" class="btn btn-default"><?php __('Reset') ?></a>
					<ul class="list-inline"> 
						<!--li style="width: 50px;padding-top: 10px;"><?php //__('Weather'); ?></li-->
						<li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
						
						<li style="margin-left: 90px;">
						<input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["rank"] == 'up' ? 'checked' : 'checked'; ?> value="up" name="data[ProjectAmr][rank][]" type="radio" /> <img title="Up"  src="<?php echo $html->url('/img/up.png') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["rank"] == 'down' ? 'checked' : ''; ?> value="down" name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;" /> <img title="Down" src="<?php echo $html->url('/img/down.png') ?>"  /></li>
						<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["rank"] == 'mid' ? 'checked' : ''; ?> value="mid" name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;"   /> <img title="Mid"  src="<?php echo $html->url('/img/mid.png');?>" style="width: 50px; !important"/></li>
					</ul>

					<p class="text-danger">
						<?php 
							if( !isset($this->data['Project']['last_modified']) || !($time = $this->data['Project']['last_modified']) ){
								$time = $projectName['Project']['updated'];
							}
							$updated = $time ? date('H:i:s A d/m/Y', $time) : '../../....';
							$byEmployee = !empty($projectName['Project']['update_by_employee']) ? $projectName['Project']['update_by_employee'] : 'N/A';
							echo __('Last Update: ', true) . $updated . __(' by ', true) . $byEmployee;
						?>
					</p>

				</div>
			</div>
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
	$file = 'widgets/mkpi-' . $field;
	if( file_exists(ELEMENTS . DS . $file . '.ctp') ){
		echo '<div class="kpi-widget kpi-visible-' . $visible . '">';
		echo $this->element($file, array(
			'type' => $type
		));
		echo '</div>';
	}
}
?>
			
		</div>
	</div>
</section>	
<?php echo $this->element('dialog_projects') ?>
<?php echo $validation->bind("ProjectAmr"); ?>
<?php echo $html->script('jquery.ba-bbq.min'); ?> 
<?php echo $html->script('jquery.multiSelect'); ?>

<style type="text/css">
	.setvalidation{
		border-color: red !important;
	}
</style>
<script language="javascript">  
	var projectName = <?php echo json_encode($projectName['Project']); ?>;
	$(document).ready(function () {   
		// dong ho progress

		var years    = <?php echo json_encode($setYear); ?>,
		manDays    = <?php echo json_encode($manDays); ?>,
		dataSets    = <?php echo !empty($dataSets) ? json_encode($dataSets) : json_encode(array()); ?>;
		var settings = {
				title: "<?php echo __('M.D Planed Follow Up', true);?>",
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
				colorScheme: 'scheme01',
				seriesGroups:
					[
						{
							type: 'line',
							showLabels: true,//default
							valueAxis:
							{
								axisSize: 'auto',
								minValue: 0,
								maxValue: manDays,
								unitInterval: manDays/10,
								description: '',
								displayValueAxis: true
							},
							series: [
									//{ dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
									{ dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 10}, color: '#AA4643'},
									{ dataField: 'validated', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: -10}, color: '#829A50'}
								  
								]
						},
						{
							type: 'rangecolumn',
							showLabels: true,
							showLegend: false,
							columnsGapPercent: 100,
							valueAxis: {
								minValue: 0,
								maxValue: manDays,
								unitInterval: manDays/10,
								description: '',
								displayValueAxis: false
								
							},
							series: [
										{ 
											dataFieldFrom: 'validated', 
											dataFieldTo: 'consumed',
											//displayText: 'Different between Consumed and Validated',
											formatFunction: caculate,
											color: '#FF0000',
											labelOffset: {x: 20, y: 0}
										}
									]
							
						}
					]
			}
			// dash board budget external
			<?php
			foreach($dataExternals as $_external=> $_dataExternal)
			{ ?>
				// dong ho progress
				var years    = <?php echo json_encode($_dataExternal['setYearExternal']); ?>,
				manDays    = <?php echo json_encode($_dataExternal['manDayExternals']); ?>,
				dataSets    = <?php echo json_encode($_dataExternal['dataSetsExternal']); ?>;
				var settingsExternal = {
						title: "<?php echo $_dataExternal['setProviderName']; echo " : "; echo $_dataExternal['setNameExternal']; echo " "; echo __('M.D Planed Follow Up', true);?>",
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
						colorScheme: 'scheme01',
						seriesGroups:
							[
								{
									type: 'line',
									showLabels: true,//default
									valueAxis:
									{
										axisSize: 'auto',
										minValue: 0,
										maxValue: manDays,
										unitInterval: manDays/10,
										description: '',
										displayValueAxis: true
									},
									series: [
											//{ dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
											{ dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 10}, color: '#AA4643'},
											{ dataField: 'planed', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: -10}, color: '#829A50'}
										  
										]
								},
								{
									type: 'rangecolumn',
									showLabels: true,
									showLegend: false,
									columnsGapPercent: 100,
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
		//commented by QN: in mobile, there is no tooltip, disable auto height

		// var height; 
		// $('#ProjectAmrProjectAmrSolution,#ProjectAmrProjectAmrRiskInformation,#ProjectAmrProjectAmrProblemInformation,#ProjectAmrProjectAmrSolutionDescription').focus(function(){
		// 	$(this).tooltip('disable');
		// 	height = $(this).height();
		// 	$(this).stop().animate({height : '150'} , 1000);
		// }).mouseup(function(){
		// 	$(this).tooltip('close');
		// }).blur(function(){
		// 	$(this).tooltip('option' , 'content' , $(this).val());
		// 	$(this).tooltip('enable');
		// 	$(this).stop().animate({height : height}, 1000 , function(){
		// 		$(this).css({height : ''});
		// 	});
		// }).tooltip({maxWidth : 1000, maxHeight : 300,content: function(target){
		// 		return $(target).text();
		// 	}});
		
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
			$('#ProjectAmrMobile-indexPlusForm').submit();
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
//        focusDescription();
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
		addLogSaleLead = function(){
			var rels = $('#pch_log_system_content').children().length;
			rels = rels ? parseInt(rels) + 1 : 1;
			var _name = <?php echo json_encode($employeeLoginName);?>;
			var _avatarOfEmployeeLogin = <?php echo json_encode($avatarEmployeeLogin);?>;
			var _date = setAndGetTimeOfParis();
			_name += ' ' + _date;
			var _onchange = 'onchange=\'updateLogSystem("' + rels + '", "' + -1 + '");\'';
			var logSystemHtml = 
				'<div class="pch_log_system" rels="' + rels + '">' + 
					'<div class="input-group">' +
						'<span class="input-group-addon no-padding" id="sizing-addon2"><img class="full-width" id="logAvatar_' + rels + '" src="/files/avatar_employ/' + companyName + '/' + employeeLoginId + '/' + _avatarOfEmployeeLogin + '" /></span>' +
						'<input id="logName_' + rels + '" readonly="readonly" class="form-control" aria-describedby="sizing-addon2" value="' + _name + '" />' +
					'</div>' +
					'<div class="pch_log_description">' +
						'<textarea class="form-control" id="logDes_' + rels + '" ' + _onchange + '></textarea>' +
					'</div>' +
				'</div>';
			$('#pch_log_system_content').prepend(logSystemHtml);
			//focusDescription();
			$('#logDes_' + rels).focus();
		};
		function addLog(id, prefix){
			var rels = $('#' + id + ' .pch_log_system_content').children().length;
			rels = rels ? parseInt(rels) + 1 : 1;
			var _name = employeeLoginName;
			var _date = setAndGetTimeOfParis();
			_name += ' ' + _date;
			var _onchange = 'onchange=\'updateLog("' + rels + '", "' + prefix + '", "0");\'';
			var logSystemHtml = 
				'<div class="pch_log_system" rels="' + rels + '">' + 
					'<div class="input-group">' +
						'<span class="input-group-addon no-padding" id="sizing-addon2"><img class="full-width" id="' + prefix + 'Avatar_' + rels + '" src="/files/avatar_employ/' + companyName + '/' + employeeLoginId + '/' + avatar + '" /></span>'+
						'<input id="' + prefix + '_' + rels + '" readonly="readonly" class="form-control" aria-describedby="sizing-addon2" value="' + _name + '" />' +
					'</div>' +
					'<div class="pch_log_description">' +
						'<textarea class="form-control" id="' + prefix + 'Des_' + rels + '" ' + _onchange + '></textarea>' +
					'</div>' +
				'</div>';
			$('#' + id + ' .pch_log_system_content').prepend(logSystemHtml);
			//focusDescription();
			$('#' + prefix + 'Des_' + rels).focus();
		}
		function updateLog(key, prefix, idOfLog){
			if($('#' + prefix + 'Des_' + key).val()){
				$('#' + prefix + 'Des_' + key).addClass('pch_loading');
				$('#' + prefix + 'Des_' + key).css('color', 'rgb(218, 215, 215)');
				setTimeout(function(){
					$.ajax({
						url: '<?php echo $html->url(array('action' => 'update_data_log')); ?>',
						async: false, 
						type : 'POST',
						dataType : 'json', 
						data: {
							id: idOfLog,
							company_id: company_id,
							model: prefix,
							model_id: projectName['id'], 
							name: $('#' + prefix + '_' + key).val(),
							description: $('#' + prefix + 'Des_' + key).val(),
							employee_id: employeeLoginId,
							update_by_employee: employeeLoginName
						},
						success:function(_idOfLog) {
							if( _idOfLog ){
								$('#' + prefix + 'Des_' + key).removeAttr('onchange');
								$('#' + prefix + 'Des_' + key).attr('onchange', 'updateLog("' + key + '", "' + prefix + '", "' + _idOfLog + '");');
							}
						},
						complete: function(){
							setTimeout(function(){
								$('#' + prefix + 'Des_' + key).removeClass('pch_loading');
								$('#' + prefix + 'Des_' + key).css('color', '#3BBD43');
							}, 200);
						}
					});
				}, 200);
			}
		}
		/**
		 * Save Log
		 */
		updateLogSystem = function(key, idOfLog){
			if($('#logDes_' + key).val()){
				$('#logDes_' + key).addClass('pch_loading');
				$('#logDes_' + key).css('color', 'rgb(218, 215, 215)');
				setTimeout(function(){
					$.ajax({
						url: '<?php echo $html->url(array('action' => 'update_data_log')); ?>',
						async: false, 
						type : 'POST',
						dataType : 'json', 
						data: {
							id: idOfLog,
							company_id: company_id,
							model: 'ProjectAmr',
							model_id: projectName['id'], 
							name: $('#logName_' + key).val(),
							description: $('#logDes_' + key).val(),
							employee_id: employeeLoginId,
							update_by_employee: employeeLoginName
						},
						success:function(data) {
							var _idOfLog = data;
							$('#logDes_' + key).removeAttr('onchange');
							$('#logDes_' + key).attr('onchange', 'updateLogSystem("' + key + '", "' + _idOfLog + '");');
							setTimeout(function(){
								$('#logDes_' + key).removeClass('pch_loading');
								$('#logDes_' + key).css('color', '#3BBD43');
							}, 200);
						}
					});
				}, 200);
			}
		};

		function focusDescription(){
			var pos = $('.pch_log').position();
			$('#pch_log_system_content').scrollTop(20);
			var target = $('.form-control');
			target.focus(function(){
				console.log($(this).height(80));
			}).blur(function(){
				$(this).height('auto')
			});
			/*var target = $('.form-control');
			target.focus(function(){
				var th = target.closest('.pch_log');
				var t = th.height()-38;
				$(this).css({
					position: 'fixed',
					left: '50%',
					top : '50%',
					width: '100%',
					zIndex: '999',
					bottom: 0,
					border: '1px solid #e0e0e0'
				}).animate({
					height: t + 'px',
				}, 100);
			}).blur(function(){
				$(this).css({
					position: 'static',
					width: '99%',
					zIndex: '1',
					bottom: '',
					left: '',
					border: 'none',
					borderRight: '1px solid #e0e0e0'
				}).animate({
					height: '32px',
				}, 100);
			});*/
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

</script>