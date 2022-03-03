<?php echo $html->script('jquery.validation.min');?>
<?php echo $html->css('jquery.multiSelect');?> 
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>

<style>
.error-message {
    color: #FF0000;
    margin-left: 35px; 
}
</style>
<div id="wd-container-main" class="wd-project-detail">    
	<div class="wd-layout">
		<?php echo $this->element("project_top_menu")?>
        <div class="wd-main-content">
			<div class="wd-title">
				<h2 class="wd-t1"><?php __("Projects")?></h2>
				<a href="<?php echo $html->url("/projects/add")?>" class="wd-add-project"><span><?php __('Add Project')?></span></a>				
				<a href="<?php echo $html->url("/user_views/export_project_amr/".$view_id."/".$projectName['Project']['id'])?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export Excel')?></span></a>				
				<ul class="wd-breadcrumb">
					<li><a class="wd-fist-link" href="<?php echo $html->url('/projects/') ?>"><?php __("Projects")?></a></li>
					<li><a href="#"><?php __("Project AMR")?></a></li>
				</ul>
			</div>
            <div class="wd-tab">
				<?php echo $this->element('project_tab_view') ?>
				<div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
					<div class="wd-section" id="wd-fragment-1">
					    
						<h2 class="wd-t2"><?php __('Project AMR')?></h2>
						<?php
						$view_content = $this->Xml->unserialize($view_content);
						if (!isset($view_content["UserView"]['ProjectAmr'])) {
							$view_content = '<user_view>
												<project_amr weather = "Weather" />
												<project_amr project_amr_program_id = "Program" />
												<project_amr project_amr_sub_program_id = "Sub Program" />
												<project_amr project_amr_category_id = "Category" />
												<project_amr project_amr_sub_category_id = "Sub Category" />
												<project_amr project_manager_id = "Project Manager" />
												<project_amr budget = "Budget" />
												<project_amr project_amr_status_id = "Status" />
												<project_amr project_amr_mep_date = "MEP Date" />
												<project_amr project_amr_progression = "Progression" />
												<project_amr project_phases_id = "Phase" />
												<project_amr project_amr_cost_control_id = "Cost Control" />
												<project_amr project_amr_organization_id = "Organization" />
												<project_amr project_amr_plan_id = "Planning" />
												<project_amr project_amr_perimeter_id = "Perimeter" />
												<project_amr project_amr_risk_control_id = "Risk Control" />
												<project_amr project_amr_problem_control_id = "Problem Control" />
												<project_amr project_amr_risk_information = "Risk Information" />
												<project_amr project_amr_problem_information = "Problem Information" />
												<project_amr project_amr_solution = "Solution" />
												<project_amr project_amr_solution_description = "Solution Description" />
											</user_view>';
							$view_content = $this->Xml->unserialize($view_content);
						}
						if (isset($view_content["UserView"]['ProjectAmr'])){
						    foreach ($view_content as $key=>$value){
								foreach ($value["ProjectAmr"] as $key1=>$value1) {
											if (!is_array($value1)) {
											unset($view_content["UserView"]["ProjectAmr"]);
											$view_content["UserView"]["ProjectAmr"]['0'] = $value["ProjectAmr"];
											}
								}
							}
							$imax=sizeof($view_content['UserView']['ProjectAmr'])/2;
							$i=0;
						    echo $this->Form->create('UserView');
                            echo $this->Form->input('project_id', array('type'=>'hidden', 'name'=>'data[ProjectAmr][project_id]', 'value'=>$project_id));
                            echo $this->Form->input('id', array('type'=>'hidden', 'name'=>'data[ProjectAmr][id]', 'value'=>($this->data['ProjectAmr']['id'])?$this->data['ProjectAmr']['id']:""));
                            App::import("vendor", "str_utility");
                            $str_utility = new str_utility();
                        ?>
                        <fieldset>
						    <?php 
							  $start_date=$str_utility->convertToVNDate($start_date);
							  $end_date=$str_utility->convertToVNDate($end_date);
							  $end_plan_date=$str_utility->convertToVNDate($end_plan_date);
							  echo $this->Form->input('project_start_date', array('type'=>'hidden', 'value'=>empty($start_date)?"":$start_date));
							  if ($end_date == "") $end_date = $end_plan_date;
							  echo $this->Form->input('project_end_date', array('type'=>'hidden', 'value'=>empty($end_date)?"":$end_date));
							?> 
							<div class="wd-scroll-form" style="height: auto" >
							<div class="wd-left-content">
								<?php
								foreach ($view_content as $key=>$value) {
								if (isset($value["ProjectAmr"])) {
								foreach ($value["ProjectAmr"] as $key1=>$value1) {

								foreach ($value1 as $field_name=>$alias) {
								
								switch($field_name){
									case "weather":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;
									}
									?>													 
										<div class="wd-input">
											<label for="weather"><?php __("Weather");?></label>
											<ul style="float: left; display: inline;"> 
												<li style="float: left; width: 100px;"><input style="width: 25px;" <?php echo $this->data["ProjectAmr"]["weather"]=='sun'? 'checked': 'checked'; ?> value="sun" name="data[ProjectAmr][weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png')?>"  /></li>
												<li style="float: left; width: 100px;"><input type="radio" <?php echo $this->data["ProjectAmr"]["weather"]=='cloud'? 'checked': ''; ?> value="cloud" name="data[ProjectAmr][weather][]" style="width: 25px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png')?>"  /></li>
												<li style="float: left; width: 100px;"><input type="radio" <?php echo $this->data["ProjectAmr"]["weather"]=='rain'? 'checked': ''; ?> value="rain" name="data[ProjectAmr][weather][]" style="width: 25px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png')?>"  /></li>
											</ul>
										</div>
									<?php break;}
									case "project_amr_program_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="program"><?php __("Program")?></label>
											<?php echo $this->Form->input('project_amr_program_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_program_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_program_id']:"",
											"empty"=>__("-- Select Program-- ", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_category_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>

										<div class="wd-input">
											<label for="category"><?php __("Category")?></label>
											<?php echo $this->Form->input('project_amr_category_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_category_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_category_id']:"",
											"empty"=>__("-- Select Category --", true),
											));?>
										</div>
									<?php break;
									}
									case "project_manager_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input" style="padding-top:6px">
											<label for="project-manager"><?php __("Project Manager")?></label>
									<!--		<p style="padding-top:6px">-->
											<?php 
											/*	if(empty($this->data['ProjectAmr']['project_manager_id'])){
													echo $project_manager['Employee']['fullname'];
													echo $this->Form->input('project_manager_id', array('div'=>false, 'label'=>false, 
															'name'=>'data[ProjectAmr][project_manager_id]',
															'value'=>$project_manager['Employee']['id'],
															'type'=>'hidden'
															));
												}
												else echo $projectManagers[$this->data['ProjectAmr']['project_manager_id']];   
											*/
                                                echo $this->Form->input('project_amr_manager_id', array('div'=>false, 'label'=>false, 
																			'name'=>'data[ProjectAmr][project_manager_id]',
																			'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_manager_id']:"",
																			"empty"=>__("-- Select Project Manager --", true),
																			"options"=>$projectManagers
												  ));											
											?>
									<!--		</p>			-->					
										</div>
									<?php break;}
									case "project_amr_status_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="status"><?php __("Status")?></label>
											<?php echo $this->Form->input('project_amr_status_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_status_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_status_id']:"",
											"empty"=>__("-- Select Status --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_progression":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input wd-input-80">
											<label for="progression"><?php __("Progression")?></label>
											<?php echo $this->Form->input('project_amr_progression', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_progression]',
											//'value'=>(!empty($this->data['ProjectAmr']))?(strpos($this->data['ProjectAmr']['project_amr_progression'], "%")?$this->data['ProjectAmr']['project_amr_progression']:($this->data['ProjectAmr']['project_amr_progression'] + 0)."%"):"0%"
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_progression']:"",
											));?>
										</div>
									<?php break;}
									case "project_amr_cost_control_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="cost-control"><?php __("Cost Control")?></label>
											<?php echo $this->Form->input('project_amr_cost_control_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_cost_control_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_cost_control_id']:"",
											"empty"=>__("-- Select Cost Control --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_plan_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="project-plan"><?php __("Planning")?></label>
											<?php echo $this->Form->input('project_amr_plan_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_plan_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_plan_id']:"",
											"empty"=>__("-- Select Plan --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_risk_control_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="risk-control"><?php __("Risk Control")?></label>
											<?php echo $this->Form->input('project_amr_risk_control_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_risk_control_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_risk_control_id']:"",
											"empty"=>__("-- Select Risk Control --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_sub_program_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="sub-program"><?php __("Sub Program")?></label>
											<?php echo $this->Form->input('project_amr_sub_program_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_sub_program_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_sub_program_id']:"",
											"empty"=>__("-- Select Sub Program --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_sub_category_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="sub-category"><?php __("Sub Category")?></label>
											<?php echo $this->Form->input('project_amr_sub_category_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_sub_category_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_sub_category_id']:"",
											"empty"=>__("-- Select Sub Category --", true),
											));?>
										</div>
									<?php break;}
									case "amr_budget":{ 
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input wd-input-80">
											<label for="budget"><?php __("Budget")?></label>
											<?php echo $this->Form->input('budget', array('div'=>false, 'label'=>false, 'style'=>'width:173px', 
											'name'=>'data[ProjectAmr][budget]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['budget']:"0"
											));?>    
											<?php echo $this->Form->input('amr_currency_id', array('div'=>false, 'label'=>false,
											'name'=>'data[ProjectAmr][currency_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['currency_id']:"",
											"empty"=>__("-- Currency --", true),
											"options"=>$currency_name
											));?>            
										</div>   
									<?php break;}
									case "project_amr_mep_date":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input wd-calendar">
											<label for="startdate"><?php __("MEP Date")?></label>
											<?php
											echo $this->Form->input('project_amr_mep_date', array('div'=>false, 
											'label'=>false,
											'maxlength'=>'10',
											'type'=>'text',
											'name'=>'data[ProjectAmr][project_amr_mep_date]',
											'value'=>(!empty($this->data['ProjectAmr']))?$str_utility->convertToVNDate($this->data['ProjectAmr']['project_amr_mep_date']):"",
											"class"=>"placeholder","placeholder"=>__("(dd-mm-yyyy)(".$start_date. " to ".$end_date.")",true)
											));
											?>
										</div>
									<?php break;}
									case "project_phases_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="current-phase"><?php __("Current Phase")?></label>
											<?php echo $this->Form->input('project_amr_phase_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_phases_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_phases_id']:"",
											"empty"=>__("-- Select Phase --", true),
											"options"=>$ProjectPhases
											));?>
										</div>
									<?php break;}
									case "project_amr_organization_id":{ 
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>									 
										<div class="wd-input">
											<label for="organization"><?php __("Organization")?></label>
											<?php echo $this->Form->input('project_amr_organization_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_organization_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_organization_id']:"",
											"empty"=>__("-- Select Organization --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_perimeter_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="perimeter"><?php __("Perimeter")?></label>
											<?php echo $this->Form->input('project_amr_perimeter_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_perimeter_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_perimeter_id']:"",
											"empty"=>__("-- Select Perimeter --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_problem_control_id":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label for="problem-control"><?php __("Problem Control")?></label>
											<?php echo $this->Form->input('project_amr_problem_control_id', array('div'=>false, 'label'=>false, 
											'name'=>'data[ProjectAmr][project_amr_problem_control_id]',
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_problem_control_id']:"",
											"empty"=>__("-- Select Problem Control --", true),
											));?>
										</div>
									<?php break;}
									case "project_amr_risk_information":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label><?php __("Risk Information")?></label>
											<?php echo $this->Form->input('project_amr_risk_information', array('type'=>'textarea', 
											'name'=>'data[ProjectAmr][project_amr_risk_information]', 
											'div'=>false, 'label'=>false, "style"=>"height: 30px; width: 63%;",
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_risk_information']:""
											));?>
										</div>
									<?php break;}
									case "project_amr_problem_information":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label><?php __("Problem Information")?></label>
											<?php echo $this->Form->input('project_amr_problem_information', array('type'=>'textarea', 
											'name'=>'data[ProjectAmr][project_amr_problem_information]', 
											'div'=>false, 'label'=>false, "style"=>"height: 30px; width: 63%;",
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_problem_information']:""
											));?>
										</div>
									<?php break;}
									case "project_amr_solution":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label><?php __("Solution")?></label>
											<?php echo $this->Form->input('project_amr_solution', array('type'=>'textarea', 'div'=>false, 
											'name'=>'data[ProjectAmr][project_amr_solution]', 
											'label'=>false, "style"=>"height: 30px; width: 63%;",
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_solution']:""
											));?>
										</div>
									<?php break;}
									case "project_amr_solution_description":{
										if($i>=$imax){ echo "</div>";
										echo '<div class="wd-right-content">';
										}
										else {$i++;}
									?>
										<div class="wd-input">
											<label><?php __("Solution Description")?></label>
											<?php echo $this->Form->input('project_amr_solution_description', array('type'=>'textarea', 'div'=>false, 
											'name'=>'data[ProjectAmr][project_amr_solution_description]', 
											'label'=>false, "style"=>"height: 30px; width: 63%;",
											'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_solution_description']:""
											));?>
										</div>
									<?php break;}

								}
							}
							}
							}
							}
							?>
							</div>     
							</div>	
                            <div class="wd-submit">
								<input type="submit" id="btnSave" value="" class="wd-save"/>
								<a href="" class="wd-reset"><?php __('Reset')?></a>
							</div>
                        </fieldset>
						<?php echo $this->Form->end(); ?>    
                        </form>
						<?php 
						}
						?>
 					</div>					
				</div>
			</div>
		</div>
	</div>	
</div>	
<?php echo $validation->bind("ProjectAmr"); ?>
<?php echo $html->script('jquery.ba-bbq.min');?> 
<?php echo $html->script('jquery.multiSelect');?>
<script language="javascript">   
    var tabs = $(".wd-tab");    
    var tab_a_selector = 'ul.ui-tabs-nav a';
    var tab_a_active = 'li.ui-state-active a';
    var cache = {};
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
    
    $(".wd-table table").dataTable();
      
    $('#UserViewProjectAmrMepDate').datepicker({
        showOn          : 'button',
        buttonImage     : '<?php echo $html->url("/img/front/calendar.gif")?>',
        buttonImageOnly : true,
        dateFormat      : 'dd-mm-yy'
    });
    
	function isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	
    $('#btnSave').click(function(){
		$("#flashMessage").hide();
		$('div.error-message').remove();
		$("div.wd-input input, select").removeClass("form-error");
		var flag1=true, flag2=true;flag3=true;
		if(!numberTest()) flag3=false;
		if (!(isDate('UserViewProjectAmrMepDate'))) {
		   	var endDate = $("#UserViewProjectAmrMepDate");
			endDate.addClass("form-error");
			var parentElem = endDate.parent();
			parentElem.addClass("error");
			parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
		    flag2=false;
        }
		else 
		{
		  if (!compareDate_1('UserViewProjectStartDate','UserViewProjectAmrMepDate', 'UserViewProjectAmrMepDate') || !compareDate_1('UserViewProjectAmrMepDate','UserViewProjectEndDate', 'UserViewProjectAmrMepDate')) 
					flag2 = false;
		}
		return flag1&&flag2 && flag3;
	});
    function numberTest(){
		var rule = /^([0-9]*)$/;
		var x=$('#UserViewProjectAmrBudget').val();
		var x2=$('#UserViewProjectAmrProgression').val();
		if(x2!=""){
			var flagg=true;flagg1=true;
			$('div.error-message').remove();
    
			if(!rule.test(x2)||x2<0||x2>100  ){
				var fomrerror = $("#UserViewProjectAmrProgression");
			    if(fomrerror.length==0)flagg=true;
				else{
					fomrerror.addClass("form-error");
					var parentElem = fomrerror.parent();
					parentElem.addClass("error");
					parentElem.append('<div class="error-message">'+"<?php __("The Progression must be a number 0-100 ") ?>"+'</div>');
					flagg= false;
				}	
			}
			else{
				var fomrerror = $("#UserViewProjectAmrProgression");
				fomrerror.removeClass("form-error");
				flagg=true;
			}
			if(x!="" && !rule.test(x)){
	            var fomrerror = $("#UserViewProjectAmrBudget");
				if(fomrerror.length==0)flagg1=true;
				else{
					fomrerror.addClass("form-error");
					var parentElem = fomrerror.parent();
					parentElem.addClass("error");
					parentElem.append('<div class="error-message">'+"<?php __("The Budget must be a number at least 0 ") ?>"+'</div>');
					flagg1= false;
				}
			}
			else  {
				var fomrerror = $("#UserViewProjectAmrBudget");
				fomrerror.removeClass("form-error");
				flagg1=true;
            
			}
			return (flagg && flagg1);
		}
		else return true;
    }
       // Script for  subprogram
    $("#UserViewProjectAmrProgramId").change(function(){
        var id = $(this).val();
        $.ajax({
            url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id,
            beforeSend: function() { $("#UserViewProjectAmrSubProgramId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#UserViewProjectAmrSubProgramId").html(data);
            } 
        });
    });
	
	$("#UserViewProjectAmrCategoryId").change(function(){
        var id = $(this).val();
        $.ajax({
            url: '<?php echo $html->url('/project_amrs/get_sub_category/') ?>' + id,
            beforeSend: function() { $("#UserViewProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#UserViewProjectAmrSubCategoryId").html(data);
            } 
        });
    });
	
	$(document).ready(function () {
		var valueInDB_pro = $("#UserViewProjectAmrSubProgramId").val();
		var valueInDB_cat = $("#UserViewProjectAmrSubCategoryId").val();
		var AMRpro_id = $("#UserViewProjectAmrProgramId").val();
		
		if(AMRpro_id==""){
			$("#UserViewProjectAmrSubProgramId").html("<option></option>");
		}
		else{
			$.ajax({
				url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + AMRpro_id,
				beforeSend: function() { $("#UserViewProjectAmrSubProgramId").html("<option>Loading...</option>"); },
				success: function(data) {
					$("#UserViewProjectAmrSubProgramId").html(data);
					$("#UserViewProjectAmrSubProgramId").val(valueInDB_pro);
				} 
			});
		}
		
		var AMRcat_id = $("#UserViewProjectAmrCategoryId").val();
		if(AMRcat_id==""){
			$("#UserViewProjectAmrSubCategoryId").html("<option></option>");
		}
		else{
			$.ajax({
				url: '<?php echo $html->url('/project_amrs/get_sub_category/') ?>' + AMRcat_id,
				beforeSend: function() { $("#UserViewProjectAmrSubCategoryId").html("<option>Loading...</option>"); },
				success: function(data) {
					$("#UserViewProjectAmrSubCategoryId").html(data);
					$("#UserViewProjectAmrSubCategoryId").val(valueInDB_cat);
				} 
			});
		}
	});
	function compareDate_1(startDateId, endDateId, obj){
		var tmp = compareDate(startDateId,endDateId);
		if(tmp==1){
			var endDate = $("#"+obj);
			var parentElem = endDate.parent();
			parentElem.addClass("error");
            endDate.addClass("form-error");
			parentElem.append('<div class="error-message"><?php echo sprintf(__("MEP date must from %s to %s", true), $start_date, $end_date) ?></div>');
			return false;
		}
		else return true;
	}
	checkConsultant();
    function checkConsultant(){
		<?php
			$employee_info = $this->Session->read('Auth.employee_info');
			if($employee_info["Employee"]['is_sas']==0&&$employee_info["Role"]["name"]=="conslt") {
		?>
			$(".wd-submit").hide();
			$(".wd-add-project").hide();
			return false;
		<?php	
			}else echo "return true";
			
		?>
	}
</script>