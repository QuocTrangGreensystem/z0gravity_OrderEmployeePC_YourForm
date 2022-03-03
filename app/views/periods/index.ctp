<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
	.wd-list-project .wd-tab .wd-content label {
		width: auto;
		margin-top: 7px;
		padding-right: 10px;
	}
</style>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
$months = Array(
	'0'=> '',
	'1'=>"January",
	'2'=>"February",
	'3'=>"March",
	'4'=>"April",
	'5'=>"May",
	'6'=>"June",
	'7'=>"July",
	'8'=>"August",
	'9'=>"September",
	'10'=>"October",
	'11'=>"November",
	'12'=>"December"
);
$YEAR = date('Y',time());
$prevYear = $year - 1;
$nextYear = $year + 1;
?>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                //debug()
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content paddingTop">
                               		<div class="wd-title">
                                    <a id="absence-prev" href="<?php echo $this->Html->url( array('action' => 'index/' . $prevYear )); ?>" >
                                        <span><?php __('Next') ?></span>
                                    </a>
                                    
                                    <span class="currentWeek"><?php echo $year; ?></span>
                                                    
                                    <a id="absence-next" href="<?php echo $this->Html->url( array('action' => 'index/' . $nextYear )); ?>" >
                                        <span><?php __('Next') ?></span>
                                    </a>
                                    </div>
                                    <p style="font-weight: bold; font-size: 16px; color: red;"><?php echo __('WARNING: ') . __('If you change one parameter ,  the staffing has to be re-build')?></p>
                                    <table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                        <thead>
                                        	<tr class="wd-header">
                                            <?php
											$htmlStart = '';
											$htmlEnd = '';
											//$input = '<input class="datepicker" value="%value" id="month_%y_%m" />';
											//$input = '<input class="activity-datepicker hasDatepicker datepicker" value="" id="month_%y_%m" />';
											$input = '<input class="datepicker %t" readonly="readonly" value="%v" id="01-%m-'.$year.'-%t" />';
											foreach($months as $key => $value)
											{
												$keyTime = strtotime('01-'.$key.'-'.$year);
												$valueStart = $valueEnd = '';
												if(isset($results[$keyTime]))
												{
													$valueStart = date('d-m-Y',$results[$keyTime]['start']);
													$valueEnd = date('d-m-Y',$results[$keyTime]['end']);
												}
												$cls = 'colMonth' ;
												if($key == 0)
												{
													$htmElmStart = __('Start Date', true);
													$htmElmEnd = __('End Date', true);
													$cls = '';
												}
												else
												{
													$inputTmp = str_replace('%m',$key,$input);
													$inputTmpEnd = $inputTmp ;
													$htmElmStart = str_replace('%t','start',$inputTmp);
													$htmElmStart = str_replace('%v',$valueStart,$htmElmStart);													
													$htmElmEnd = str_replace('%t','end',$inputTmpEnd);
													$htmElmEnd = str_replace('%v',$valueEnd,$htmElmEnd);
												}
												
												$htmlStart .= "<td class=\"$cls\">$htmElmStart</td>";
												$htmlEnd .= "<td class=\"$cls\">$htmElmEnd</td>";
												?>
                                                <td class="<?php echo $cls; ?>"><?php __($value) ?> </td>
                                                <?php
											}
											?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class=""><?php echo $htmlStart; ?></tr>
                                            <tr class=""><?php echo $htmlEnd; ?></tr>
                                        </tbody>
                                    </table>
                                    <div class="wd-table" id="project_container">
                                	<div id="wd-select">
									 	<div class="wd-input-select">
                                            <label><?php echo __("Display data in vision staffing by month", true)?></label>
                                            <?php
												$option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('staffing_by_month_multiple', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe('staffing_by_month_multiple',this.value);",
                                                    "class" => "staffing_by_month_multiple",
                                                    "default" => &$companyConfigs['staffing_by_month_multiple'],
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
<?php echo $this->element('ajaxCommon'); ?>
<script type="text/javascript" >
$('.datepicker').datepicker({
	readonly : true,
	dateFormat : 'dd-mm-yy',
	showOn : 'focus',
	/*beforeShow : function(date, inst){
		var currentDate = $(this).datepicker('getDate');
		var id = $(this).prop('id').split('-');
		var month = id[1], year = id[2];
		if( !currentDate )
			currentDate = new Date(year, month-1, 1);
		$(this).datepicker('option', 'defaultDate', currentDate);
	},
	beforeShowDay : function(date){
		var result;
		if( $(this).hasClass('start') )result = date.getDay() == 6;
		else result = date.getDay() == 5;
		return [result, ''];
	}*/
	beforeShowDay : function(date){
		var year = <?php echo $year; ?>;
		var result;
		var bYear = date.getFullYear() != year;
		if (bYear) {
			//do nothing
		}
		else {
			result = date.getFullYear() ;
		}
		return [result, ''];
	}
});
$('.datepicker').change(function(e) {
    var $this = $(this);
	var key = this.id;
	$(loading).insertAfter('#'+key);
	var field = 'start';
	if( $(this).hasClass('end') ) 
	{
		key = key.replace('-end','');
		field = 'end';
	}
	else
	{
		key = key.replace('-start','');
	}
	var value = $this.val();
	$.ajax({
		url: '/periods/editMe/',
		data: {
			data : { value : value, field : field, key : key }
		},
		type:'POST',
		success:function(data) {
			$('#'+field).removeClass('KO');
			$('#loadingElm').remove();
		}
	});
});
function setDataForMonth(){
	
}
</script>
<style>
.ui-state-disabled{
	color:#CCC !important;
}
.colMonth{
	color:#FFF !important;
	text-align:center;
	width:7%;
}
.datepicker{
	width:90%;
	border:none;
	padding:5px;
	margin-left:-5px;
}
table tbody tr:hover td{
	background-color:transparent;
}
</style>