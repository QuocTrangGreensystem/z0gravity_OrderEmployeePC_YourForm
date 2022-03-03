<?php 

//debug($view_content);exit;
?>

<?php echo $html->script('jquery.validation.min');?>
<?php echo $html->css('jquery.multiSelect');?> 
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>

<div id="wd-container-main" class="wd-project-detail">    
	<div class="wd-layout">
		<?php echo $this->element("project_top_menu")?>
        <div class="wd-main-content">
			<div class="wd-title">
				<h2 class="wd-t1"><?php __("Projects")?></h2>
				<a href="<?php echo $html->url("/projects/add")?>" class="wd-add-project"><span><?php __('Add Project')?></span></a>
				
				<ul class="wd-breadcrumb">
					<li><a class="wd-fist-link" href="<?php echo $html->url('/projects/') ?>"><?php __("Projects")?></a></li>
					<li><a href="#"><?php __("Project AMR")?></a></li>
				</ul>
			</div>
            <div class="wd-tab">
				<?php echo $this->element('project_tab') ?>
				<div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
					<div class="wd-section" id="wd-fragment-1">
						<h2 class="wd-t2"><?php __('Project AMR')?></h2>
                        <?php 
                            echo $this->Form->create('UserView');
                            echo $this->Form->input('project_id', array('type'=>'hidden', 'name'=>'data[ProjectAmr][project_id]', 'value'=>$project_id));
                            echo $this->Form->input('id', array('type'=>'hidden', 'name'=>'data[ProjectAmr][id]', 'value'=>($this->data['ProjectAmr']['id'])?$this->data['ProjectAmr']['id']:""));
                            App::import("vendor", "str_utility");
                            $str_utility = new str_utility();
                        ?>
                        <fieldset>
							<div class="wd-scroll-form" style="height: auto;">
										<div class="wd-left-content">
											
											   <?php
												$view_content = $this->Xml->unserialize($view_content);
												$imax=sizeof($view_content['UserView']['ProjectAmr'])/2;
												//debug($imax);exit;
											   $number_of_fields = 0;
											   $i=0;
											   
												foreach ($view_content as $key=>$value) {
												//debug($value);exit;
												foreach ($value["ProjectAmr"] as $key1=>$value1) {
												
												foreach ($value1 as $field_name=>$alias) {
													$number_of_fields ++;
													
												 switch($field_name){
												   case "weather":{
													if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}					   
												  ?>								 
													<div class="wd-input">
																		  
														<label for="weather"><?php __("Weather")?></label>
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
																   $i=0;}
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
																   $i=0;}
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
																   $i=0;}
													 else {$i++;}
													?>
													
													<div class="wd-input">
														<label for="project-manager"><?php __("Project Manager")?></label>
														<?php echo $this->Form->input('project_amr_manager_id', array('div'=>false, 'label'=>false, 
																						'name'=>'data[ProjectAmr][project_manager_id]',
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_manager_id']:"",
																						"empty"=>__("-- Select Project Manager --", true),
																						"options"=>$projectManagers
																						));?>
													</div>
													<?php break;}
													case "project_amr_status_id":{
													 if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
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
																   $i=0;}
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
																   $i=0;}
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
																   $i=0;}
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
																   $i=0;}
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
													?>
												
												
													 <?php 
													 case "project_amr_sub_program_id":{
													  if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
													 ?>
													<div class="wd-input" >
													<label>&nbsp;</label>
													</div>
													<div class="wd-input">
														<label for="sub-program"><?php __("Sub Program")?></label>
														<?php echo $this->Form->input('project_amr_sub_program_id', array('div'=>false, 'label'=>false, 
																						'name'=>'data[ProjectAmr][project_amr_sub_program_id]',
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_sub_program_id']:"",
																						"empty"=>__("-- Select Sub Program --", true),
																						));?>
													</div>
													<?php break;}
													case "project_amr_category_id":{
													 if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
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
													case "budget":{ 
													 if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
													?>
													<div class="wd-input wd-input-80">
														<label for="budget"><?php __("Budget")?></label>
														<?php echo $this->Form->input('budget', array('div'=>false, 'label'=>false, 'style'=>'width:173px', 
																						'name'=>'data[ProjectAmr][budget]',
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['budget']:"0"
																						));?>    
													 </div>
													 <?php break;}
													 case "currency_id":{
													 if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
													 ?>
													<div class="wd-input wd-input-80">
														<label for="budget"><?php __("Current")?></label>
														<?php echo $this->Form->input('amr_currency_id', array('div'=>false, 'label'=>false,
																						'name'=>'data[ProjectAmr][currency_id]',
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['currency_id']:""
																					));?>            
													</div>       
													<?php break;}
													
													case "project_amr_mep_date":{
													 if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
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
																									"class"=>"placeholder","placeholder"=>__("(dd-mm-yy)",true)
																									));
														?>
													</div>
													<?php break;}
													case "project_phases_id":{
													 if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
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
																   $i=0;}
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
																   $i=0;}
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
																   $i=0;}
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
													?>

												 <?php case "project_amr_risk_control_id":{
													if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
												 ?>
												<div class="wd-input">
													<label><?php __("Risk Information")?></label>
													<?php echo $this->Form->input('project_amr_risk_information', array('type'=>'textarea', 
																					'name'=>'data[ProjectAmr][project_amr_risk_information]', 
																					'div'=>false, 'label'=>false, "style"=>"height: 30px; width: 737px;",
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_risk_information']:""
																						));?>
												</div>
												 <?php break;}
												 case "project_amr_problem_information":{
												  if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
												  ?>
												<div class="wd-input">
													<label><?php __("Problem Information")?></label>
													<?php echo $this->Form->input('project_amr_problem_information', array('type'=>'textarea', 
																					'name'=>'data[ProjectAmr][project_amr_problem_information]', 
																					'div'=>false, 'label'=>false, "style"=>"height: 30px; width: 737px;",
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_problem_information']:""
																						));?>
												</div>
												<?php break;}
												case "project_amr_solution":{
												if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
												?>
												<div class="wd-input">
													<label><?php __("Solution")?></label>
													<?php echo $this->Form->input('project_amr_solution', array('type'=>'textarea', 'div'=>false, 
																				'name'=>'data[ProjectAmr][project_amr_solution]', 
																				'label'=>false, "style"=>"height: 30px; width: 737px;",
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_solution']:""
																						));?>
												</div>
												<?php break;}
												case "project_amr_solution_description":{
												if($i>=$imax){ echo "</div>";
																   echo '<div class="wd-right-content">';
																   $i=0;}
													 else {$i++;}
												?>
												<div class="wd-input">
													<label><?php __("Solution Description")?></label>
													<?php echo $this->Form->input('project_amr_solution_description', array('type'=>'textarea', 'div'=>false, 
																				'name'=>'data[ProjectAmr][project_amr_solution_description]', 
																				'label'=>false, "style"=>"height: 30px; width: 737px;",
																						'value'=>(!empty($this->data['ProjectAmr']))?$this->data['ProjectAmr']['project_amr_solution_description']:""
																						));?>
												</div>
												<?php break;
												
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
 					</div>					
				</div>
			</div>
		</div>
	</div>	
</div>	
<?php //echo $validation->bind("ProjectAmr"); ?>
<?php echo $html->script('jquery.ba-bbq.min');?> 
<?php echo $html->script('jquery.multiSelect');?>
<script language="javascript">   
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
    
    $(".wd-table table").dataTable();
      
    $('#ProjectAmrProjectAmrMepDate').datepicker({
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
		var v1 = isOnLimit("ProjectAmrBudget",'vc',0,"<?php __('The budget must be a number and at least 0.') ?>");
		var v2 = isOnLimit("ProjectAmrProjectAmrProgression",100,0,"<?php __('The progression must be a number and between 0 and 100.') ?>");
		var flag1=false, flag2=false;
		if(v1&&v2) flag1 = true;
		if (!(isDate('ProjectAmrProjectAmrMepDate'))) {
			var endDate = $("#ProjectAmrProjectAmrMepDate");
			endDate.addClass("form-error");
			var parentElem = endDate.parent();
			parentElem.addClass("error");
			parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
		}
		else flag2=true;
		return flag1&&flag2;
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
        $("#title_form_update").html("<?php __("Add a new employee for this project")?>");
        $("#project_phase_plan_id").val("");
        $("#title_form_update_phase").html("<?php __("Add a new phase planning for this project")?>");
        $("#project_milestone_id").val("");
        $("#title_form_update_milestone").html("<?php __("Add a new milestone for this project")?>");
        $("#project_task_id").val("");
        $("#title_form_update_task").html("<?php __("Add a new task for this project")?>");        
        $("#project_risk_id").val("");
        $("#title_form_update_risk").html("<?php __("Add a new risk for this project")?>");
        $("#project_issue_id").val("");
        $("#title_form_update_issue").html("<?php __("Add a new issue for this project")?>");
        $("#project_decision_id").val("");
        $("#title_form_update_decisions").html("<?php __("Add a new decision for this project")?>");
        $("#ProjectLivrableActor span").html("<?php __("Select actors") ?>");
        $("input[name='ProjectLivrableActor[]']").removeAttr("checked");
        $("input[name='ProjectLivrableActor[]']").parent().removeClass("checked");
        $("#project_livrable_id").val("");
        $("#title_form_update_livrable").html("<?php __("Add new a deliverable for this project")?>");
        $("#ProjectProjectEvolutionImpactId span").html("<?php __("Select impact") ?>");
        $("input[name='ProjectProjectEvolutionImpactId[]']").removeAttr("checked");
        $("input[name='ProjectProjectEvolutionImpactId[]']").parent().removeClass("checked");
        $("#project_evolution_id").val("");
        $("#title_form_update_evolution").html("<?php __("Add a new evolution for this project")?>");
        // HuyTD: Quick reset form command 
        $("[name*='data[ProjectAmr]']").val("");  
    });
       // Script for  subprogram
    $("#ProjectAmrProjectAmrProgramId").change(function(){
        var id = $(this).val();
        $.ajax({
            url: '<?php echo $html->url('/project_amrs/get_sub_program/') ?>' + id,
            beforeSend: function() { $("#ProjectAmrProjectAmrSubProgramId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectAmrProjectAmrSubProgramId").html(data);
            } 
        });
    });
    
</script>