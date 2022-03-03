<?php echo $html->script('jquery.validation.min');?>
<?php echo $html->css('jquery.multiSelect');?> 
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->script('validateDate'); ?>
<style>
.error-message {
    color: #FF0000;
    margin-left: 0px; 
}
</style>
<div id="wd-container-main" class="wd-project-detail">    
	<div class="wd-layout">
		<?php echo $this->element("project_top_menu")?>
        <div class="wd-main-content">
			<div class="wd-title">
				<h2 class="wd-t1"><?php __("Projects")?></h2>
				<a href="<?php echo $html->url('/projects/add') ?>" class="wd-add-project"><span><?php __('Add Project')?></span></a>
				<a href="<?php echo $html->url("/user_views/exportExcelDetail/".$view_id."/".$project_id)?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export Excel')?></span></a>								
				<ul class="wd-breadcrumb">
					<li><a class="wd-fist-link" href="<?php echo $html->url("/projects/index")?>"><?php __("Projects")?></a></li>
					<li><a href=""><?php __("Project Details")?></a></li>
				</ul>
			</div>
            
            <div class="wd-tab">
				<?php echo $this->element('project_tab_view') ?>
				<div class="wd-panel">
					<div class="wd-section" id="wd-fragment-1">
						<h2 class="wd-t2"><?php echo sprintf(__("Project details of %s", true), $project_name['Project']['project_name']) ?></h2>
                        <?php echo $this->Session->flash(); ?>
                        <?php 
                            echo $this->Form->create('Project', array('action'=>'project_detail_view'));
                            echo $this->Form->input('id');
                            App::import("vendor", "str_utility");
                            $str_utility = new str_utility();
							$view_content = $this->Xml->unserialize($view_content);
							foreach ($view_content as $key=>$value) {					
								if (isset($value["ProjectDetail"])) {
									foreach ($value["ProjectDetail"] as $key1=>$value1) {
										if (!is_array($value1)) {
											unset($view_content["UserView"]["ProjectDetail"]);
											$view_content["UserView"]["ProjectDetail"]['0'] = $value["ProjectDetail"];
										}
									}
								}
								if (isset($value["ProjectAmr"])) {
									foreach ($value["ProjectAmr"] as $key1=>$value1) {
										if (!is_array($value1)) {
											unset($view_content["UserView"]["ProjectAmr"]);
											$view_content["UserView"]["ProjectAmr"]['0'] = $value["ProjectAmr"];
										}
									}
								}	
							}
                        ?>
                        <fieldset>
    						<div class="wd-scroll-form" >
								<input type="hidden" name="ViewId" value="<?php echo $view_id?>" />
								<input type="hidden" name="ProjectId" value="<?php echo $project_id?>" />
                                <?php 
								foreach ($view_content as $key=>$value) {
								foreach ($value["ProjectDetail"] as $key1=>$value1) {
								foreach ($value1 as $field_name=>$alias) {
									switch ($field_name) { 
										case "project_name":?>
											<div class="wd-left-content">
												<div class="wd-input">
													<label for="project-name"><?php __("Project Name")?></label>
													<?php echo $this->Form->input('project_name', array('div'=>false, 'label'=>false));?>
												</div>
											</div>
										<?php break;
										case "company_id":?>
											<div class="wd-left-content">
												<div class="wd-input">
													<label for="Company"><?php __("Company")?></label>
													 <p style ="padding-top:6px;"><?php echo $name_company?>
													 </p>
												</div>
											</div>
										<?php break;										
										
										case "project_priority_id": ?>
											<div class="wd-left-content">
												<div class="wd-input">
													<label for="priority"><?php __("Priority")?></label>
													<?php echo $this->Form->input('project_priority_id', array('div'=>false, 'label'=>false, "options"=>$Priorities,'empty'=>__("--Select--", true)));?>
												</div>
											</div>
										<?php break;	
										case "project_status_id": ?>
											<div class="wd-left-content">
												<div class="wd-input">
													<label for="status"><?php __("Status")?></label>
													<?php echo $this->Form->input('project_status_id', array('div'=>false, 'label'=>false, "options"=>$Statuses,'empty'=>__("--Select--", true)));?>
												</div>
											</div>
										<?php break;
										case "project_phase_id": ?>
											<div class="wd-left-content">
												<div class="wd-input">
													<label for="current-phase"><?php __("Current Phase")?></label>
													<?php echo $this->Form->input('project_phase_id', array('div'=>false, 'label'=>false, "options"=>$ProjectPhases,'empty'=>__("--Select--", true)));?>
												</div>
											</div>
										<?php break; 
										case "project_manager_id": ?>
											<div class="wd-left-content">
												<div class="wd-input">
													<label for="project-manager"><?php __("Project Manager")?></label>
													<?php echo $this->Form->input('project_manager_id', array('div'=>false, 'label'=>false, "options"=>$projectManagers,'empty'=>__("--Select--", true)));?>
												</div>
											</div>
										<?php break;
										case "budget": ?>
											<div class="wd-left-content">
												<div class="wd-input wd-input-80">
													<label for="budget"><?php __("Budget")?></label>
													<?php echo $this->Form->input('budget', array('div'=>false, 'label'=>false, 'style'=>'width:173px'));?>                   
													<?php echo $this->Form->input('currency_id', array('div'=>false, 'label'=>false));?>
												</div>
											</div>
										<?php break;
										case "start_date":?>
											<div class="wd-left-content">
												<div class="wd-input wd-calendar">
													<label for="startdate"><?php __("Start Date")?></label>
													<?php 
													echo $this->Form->input('start_date', array('div'=>false, 
																								'label'=>false, 
																								'value'=> $str_utility->convertToVNDate($this->data["Project"]["start_date"]), 
																								'type'=>'text'));
													?>
												</div>
											</div>
										<?php break;
										case "end_date": ?>
											<div class="wd-left-content">
												<div class="wd-input wd-calendar">
													<label for="enddate"><?php __("End Date")?></label>
													<?php 
													echo $this->Form->input('end_date', array('div'=>false,
																								'label'=>false,
																								'value'=>$str_utility->convertToVNDate($this->data["Project"]["end_date"]),
																								'type'=>'text'));
													?>
												</div>
											</div>
										<?php break;
										case "planed_end_date": ?>
											<div class="wd-left-content">
												<div class="wd-input wd-calendar">
													<label for="originaldate"><?php __("Planned End Date")?></label>
													<?php 
													echo $this->Form->input('planed_end_date', array('div'=>false, 
																										'label'=>false, 
																										'value'=>$str_utility->convertToVNDate($this->data["Project"]["planed_end_date"]), 
																										'type'=>'text'));
													?>
												</div>
											</div>
										<?php break;
										case "project_objectives": ?>
											<div class="wd-input wd-area wd-none">
												<label><?php __("Project Objectives")?></label>
												<?php echo $this->Form->input('project_objectives', array('type'=>'textarea', 'div'=>false, 'label'=>false));?>
											</div>
										<?php break;
										case "constraint": ?>
											<div class="wd-input wd-area wd-none">
												<label for="constraint"><?php __("Constraint")?></label>
												<?php echo $this->Form->input('constraint', array('type'=>'textarea', 'div'=>false, 'label'=>false));?>
											</div>
											<div class="wd-input wd-area wd-none">
												<label for="remark"><?php __("Remark")?></label>
												<?php echo $this->Form->input('remark', array('type'=>'textarea', 'div'=>false, 'label'=>false));?>
											</div>
										<?php break;
									}
								}
							}
						}
						//exit;
								?>
        						
                            </div>
                            <div class="wd-submit">
    							<input type="submit" value="" onclick="return validateForm();" class="wd-save"/>
    							<a href="" class="wd-reset"><?php __('Reset')?></a>
    						</div>
                        </fieldset>
                        </form>
 					</div>					
				</div>
			</div>
		</div>
	</div>	
</div>	
<?php echo $html->script('jquery.multiSelect');?>
<?php echo $html->script('validateDate'); ?>
<?php echo $validation->bind("Project"); ?>
<script language="javascript">   
    
    $(".wd-table table").dataTable();
    
    $("#ProjectStartDate, #ProjectEndDate, #ProjectPlanedEndDate").datepicker({
        showOn          : 'button',
        buttonImage     : '<?php echo $html->url("/img/front/calendar.gif")?>',
        buttonImageOnly : true,
        dateFormat      : 'dd-mm-yy'
    });    
    
    
    function validateForm(){
		var flag = true, flag1 = true;
		$("#flashMessage").hide();
		$('div.error-message').remove();
		$("div.wd-input input, select").removeClass("form-error");
		if (!(isDate('ProjectStartDate'))) {
			var endDate = $("#ProjectStartDate");
			endDate.addClass("form-error");
			var parentElem = endDate.parent();
			parentElem.addClass("error");
			parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
			flag1 = flag = false;
		}
		if (!(isDate('ProjectEndDate'))) {
			var endDate = $("#ProjectEndDate");
			endDate.addClass("form-error");
			var parentElem = endDate.parent();
			parentElem.addClass("error");
			parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
			flag1 = flag = false;
		}
		if (!(isDate('ProjectPlanedEndDate'))) {
			var endDate = $("#ProjectPlanedEndDate");
			endDate.addClass("form-error");
			var parentElem = endDate.parent();
			parentElem.addClass("error");
			parentElem.append('<div class="error-message">'+"<?php __("Invalid Date (Valid format is dd-mm-yyyy)") ?>"+'</div>');
			flag1 = false;
		}
		if (flag) {
			if (compareDate('ProjectStartDate','ProjectEndDate') > 0 ) {
				var endDate = $("#ProjectEndDate");
				endDate.addClass("form-error");
				var parentElem = endDate.parent();
				parentElem.addClass("error");
				parentElem.append('<div class="error-message">'+"<?php __("The end date must be greater than start date.") ?>"+'</div>');
				flag1 = false;
			}
		}
		return flag1;
    }
	
</script>