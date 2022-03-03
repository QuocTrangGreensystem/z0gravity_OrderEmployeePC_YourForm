<?php echo $html->css(array('projects')); 
echo $html->css('jquery.dataTables'); 
$arrMonths = array(
	1 => 'January',
	2 => 'February',
	3 => 'March',
	4 => 'April',
	5 => 'May',
	6 => 'June',
	7 => 'July',
	8 => 'August',
	9 => 'September',
	10 => 'October',
	11 => 'November',
	12 => 'December',
	-1 => 'Check'
);
?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
.wd-input-select label{display:inline-block;min-width:370px;}
.wd-input-select{padding-top:10px;font-weight:700;}
thead th {  line-height: 23px !important; vertical-align:middle; }
#loadingElm img{margin:4px 0 0 10px;}
.val{ font-weight:bold; text-align:center;}
.inputCapacity{ width:50px !important; display:block; margin:auto; text-align:center; font-weight:bold;}
.KO{ color:#F00 !important;}
.OK{ color:#00F !important;}
.errorCapacity{ color:#F00; padding-right:10px;}
.wd-list-project .wd-tab .wd-content label{
	margin-top: 10px;
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
                            	<table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                <thead>
                                	
                                    <tr class="wd-header">
                                    <?php
									$str = "";
									$strInput = "capacity_by_month_";
									$total = 0;
									foreach($arrMonths as $key=>$name)
									{
										?>
                                        <th><?php __($name) ?></td>
                                        <?php
										$idInput = $strInput.$key;
										$val = $key != -1 ? 8.33 : 100;
										
										if($key != -1)
										{
											$valConfig = isset($companyConfigs[$idInput]) ? $companyConfigs[$idInput] : 8.33;
											$total += $valConfig;
										//$str.="<td class='val'><input onchange='checkVal(this.id,this.value,event)' data-old='".$valConfig."' name='".$idInput."' id='".$idInput."' value='".$valConfig."' class='inputCapacity' onkeypress='return checkDecimalValue(event,this.id);'  /><span class='errorCapacity' id='error_".$idInput."'></span></td>";
											$str.="<td class='val'><input name='".$idInput."' id='".$idInput."' value='".$valConfig."' class='inputCapacity' onkeypress='return checkDecimalValue(event,this.id);'  /></td>";
										}
										else
										{
											$class = $total == 100 ? 'OK' : 'KO';
											$str.="<td class='val' id='valTotal'><span class='".$class."'>".$total."%</span></td>";
										}
									}
									?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php echo $str; ?>
                                </tbody>
                                <tr><td align="center" colspan="13"><span class="errorCapacity"></span><input id="btnCheck" style="margin:auto; cursor:pointer" onclick="checkVal();" type="button" value="<?php echo __('Submit',true); ?>"  /></td></tr>
                                </table>
                                
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
									$ID = &$data['id'];
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container">
                                	<div id="wd-select">
									 	<div class="wd-input-select">
                                            <label><?php echo __("Display Real capacity in vision staffing", true) ?></label>
                                            <?php
												$option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('staffing_by_pc_display_real_capacity', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_pc_display_real_capacity',this.value);",
                                                    "class" => "staffing_by_pc_display_real_capacity",
                                                    "default" => &$companyConfigs['staffing_by_pc_display_real_capacity'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
                                         <div class="wd-input-select">
                                            <label><?php echo __("Display Theoretical capacity in vision staffing", true) ?></label>
                                            <?php
                                                echo $this->Form->input('staffing_by_pc_display_theoretical_capacity', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_pc_display_theoretical_capacity',this.value);",
                                                    "class" => "staffing_by_pc_display_theoretical_capacity",
                                                    "default" => &$companyConfigs['staffing_by_pc_display_theoretical_capacity'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Display Absence in vision staffing", true) ?></label>
                                            <?php
                                                echo $this->Form->input('staffing_by_pc_display_absence', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_pc_display_absence',this.value);",
                                                    "class" => "staffing_by_pc_display_absence",
                                                    "default" => &$companyConfigs['staffing_by_pc_display_absence'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Display working day in vision staffing", true) ?></label>
                                            <?php
                                                echo $this->Form->input('staffing_by_pc_display_working_day', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_pc_display_working_day',this.value);",
                                                    "class" => "staffing_by_pc_display_working_day",
                                                    "default" => &$companyConfigs['staffing_by_pc_display_working_day'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Display FTE real in vision staffing", true) ?></label>
                                            <?php
                                                echo $this->Form->input('staffing_by_pc_display_real_fte', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_pc_display_real_fte',this.value);",
                                                    "class" => "staffing_by_pc_display_real_fte",
                                                    "default" => &$companyConfigs['staffing_by_pc_display_real_fte'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Display FTE theoretical in vision staffing", true) ?></label>
                                            <?php
                                                echo $this->Form->input('staffing_by_pc_display_theoretical_fte', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_pc_display_theoretical_fte',this.value);",
                                                    "class" => "staffing_by_pc_display_theoretical_fte",
                                                    "default" => &$companyConfigs['staffing_by_pc_display_theoretical_fte'],
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										 <div class="wd-input-select">
                                            <label><?php echo __("Displays the absence tab", true) ?></label>
                                            <?php
												$display_absence_tab = isset($companyConfigs['display_absence_tab']) ? $companyConfigs['display_absence_tab'] : 1;
                                                echo $this->Form->input('display_absence_tab', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('display_absence_tab',this.value);",
                                                    "class" => "display_absence_tab",
                                                    "default" => &$display_absence_tab,
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										 <div class="wd-input-select">
                                            <label><?php echo __("Displays Days not validated in vision staffing", true) ?></label>
                                            <?php
												$staffing_show_days_not_validates = isset($companyConfigs['staffing_show_days_not_validates']) ? $companyConfigs['staffing_show_days_not_validates'] : 1;
                                                echo $this->Form->input('staffing_show_days_not_validates', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_show_days_not_validates',this.value);",
                                                    "class" => "staffing_show_days_not_validates",
													"rel" => "no-history",
                                                    "default" => &$staffing_show_days_not_validates,
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										 <div class="wd-input-select">
                                            <label><?php echo __("Displays Resources in vision staffing", true) ?></label>
                                            <?php
												$staffing_show_resources = isset($companyConfigs['staffing_show_resources']) ? $companyConfigs['staffing_show_resources'] : 1;
                                                echo $this->Form->input('staffing_show_resources', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_show_resources',this.value);",
                                                    "class" => "staffing_show_resources",
                                                    "default" => &$staffing_show_resources,
													"rel" => "no-history",
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										 <div class="wd-input-select">
                                            <label><?php echo __("Displays Consumed in vision staffing", true) ?></label>
                                            <?php
												$staffing_show_consumed = isset($companyConfigs['staffing_show_consumed']) ? $companyConfigs['staffing_show_consumed'] : 1;
                                                echo $this->Form->input('staffing_show_consumed', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_show_consumed',this.value);",
                                                    "class" => "staffing_show_consumed",
													"rel" => "no-history",
                                                    "default" => &$staffing_show_consumed,
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										<div class="wd-input-select">
                                            <label><?php echo __("Display the ABSENCES tab", true) ?></label>
                                            <?php
												$show_absences = isset($companyConfigs['absences_show_absences']) ? $companyConfigs['absences_show_absences'] : 0;
                                                echo $this->Form->input('absences_show_absences', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('absences_show_absences',this.value);",
                                                    "class" => "absences_show_absences",
													"rel" => "no-history",
                                                    "default" => &$show_absences,
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										<div class="wd-input-select">
                                            <label><?php echo __("Display the YOUR ABSENCE REVIEW tab", true) ?></label>
                                            <?php
												$show_your_absences = isset($companyConfigs['absences_show_your_absence_review']) ? $companyConfigs['absences_show_your_absence_review'] : 0;
                                                echo $this->Form->input('absences_show_your_absence_review', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('absences_show_your_absence_review',this.value);",
                                                    "class" => "absences_show_your_absence_review",
													"rel" => "no-history",
                                                    "default" => &$show_your_absences,
                                                    "options" => $option
                                                    ));
                                            ?>
                                        </div>
										<div class="wd-input-select">
                                            <label><?php echo __("Display the ABSENCE REVIEWS tab", true) ?></label>
                                            <?php
												$show_absences_reviews = isset($companyConfigs['absences_show_absence_reviews']) ? $companyConfigs['absences_show_absence_reviews'] : 0;
                                                echo $this->Form->input('absences_show_absence_reviews', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('absences_show_absence_reviews',this.value);",
                                                    "class" => "absences_show_absence_reviews",
													"rel" => "no-history",
                                                    "default" => &$show_absences_reviews,
                                                    "options" => $option
                                                    ));
                                            ?>
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
<?php $is_admin = (isset($employee_info['Role']['id']) && $employee_info['Role']['id'] == 2) ? 1 : 0; ?>
<script type="text/javascript" >
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
var is_admin = <?php echo json_encode($is_admin);?>;
var currentVal;
function checkDecimalValue(e,id)
{
	var key = e.keyCode ? e.keyCode : e.which;
	if(!key || key == 8 || key == 13){
		return;
	}
	var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));	
	if(val != '0' && !/^(([\-1-9][0-9]{0,6})|(0))(\.[0-9]{0,2})?$/.test(val)){
		$('#'+id).addClass('KO');
		return false;
	}
	else
	{
		//DO NOTHING		
	}
}
function checkVal()
{
	/*var total = 0;
	var myVal = 0;
	var suggestVal = 0;
	$('input.inputCapacity').each(function(index, element) {
        if(this.id != id)
		{
			var varMonth = $(element).val();
			total = total + parseFloat(varMonth);
		}
		else
		{
			myVal = parseFloat($(element).val());
		}
    });
	if((myVal + total) != 100)
	{
		suggestVal = 100 - total;
		$('#'+id).val($('#'+id).data('old'));
		$('#error_'+id).text('Suggest value : '+suggestVal);
		$('#'+id).addClass('KO');
	}
	else
	{
		$('.errorCapacity').text('');
		$('#'+id).data('old',val);
		$('#'+id).removeClass('KO');
		editMe(id,val);
	}*/
	var total = 0;
	var myVal = 0;
	$('input.inputCapacity').each(function(index, element) {
        var varMonth = $(element).val();
		varMonth = parseFloat(varMonth);
		total = total + varMonth;
		
    });
	total = parseFloat(total).toFixed(2)
	if(total!=100)
	{
		$('#valTotal span').text(total+'%');
		$('#valTotal span').removeClass('OK');
		$('#valTotal span').addClass('KO');
		$('.errorCapacity').text('Invalid!!!');
		return false;
	}
	else
	{
		$('#valTotal span').text(total+'%');
		$('#valTotal span').removeClass('KO');
		$('#valTotal span').addClass('OK');
		$('.errorCapacity').text('');
		$('input.inputCapacity').each(function(index, element) {
			editMe(this.id,$(element).val());
		});
	}
}
function editMe(field,value)
{
	if(!is_admin) return;
	$(loading).insertAfter('#'+field);
	var data = field+'/'+value;
	$.ajax({
		url: '/company_configs/editMe/',
		data: {
			data : { value : value, field : field }
		},
		type:'POST',
		success:function(data) {
			$('#'+field).removeClass('KO');
			$('#loadingElm').remove();
		}
	});
}
</script>