<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); //debug($view_fields);debug($projects);exit;                ?>
<?php echo $html->script('history_filter'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    table.display thead th {
        color: #FFFFFF;
        cursor: pointer;
        font-size: 13px;
        font-weight: normal;
        line-height: 16px;
        text-align: center;
    }
    .ui-state-default .ui-icon {
        float: right;
        margin-top: 0;
    }

</style>
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Project List", true); ?></h2>
                    <a href="javascript:void(0);" id="add_project" class="wd-add-project"><span><?php __('Add Project') ?></span></a>
                    <a href="<?php echo $html->url("/user_views/exportProjectViewToExcel/" . $view_id) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                </div>
                <?php
                //debug($amrs);
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                $view_fields = $this->Xml->unserialize($view_fields);
                foreach ($view_fields as $key => $value) {
                    if (isset($value["ProjectDetail"])) {
                        foreach ($value["ProjectDetail"] as $key1 => $value1) {
                            if (!is_array($value1)) {
                                unset($view_fields["UserView"]["ProjectDetail"]);
                                $view_fields["UserView"]["ProjectDetail"]['0'] = $value["ProjectDetail"];
                            }
                        }
                    }
                    if (isset($value["ProjectAmr"])) {
                        foreach ($value["ProjectAmr"] as $key1 => $value1) {
                            if (!is_array($value1)) {
                                unset($view_fields["UserView"]["ProjectAmr"]);
                                $view_fields["UserView"]["ProjectAmr"]['0'] = $value["ProjectAmr"];
                            }
                        }
                    }
                }
                ?>
                <?php echo $this->Session->flash(); ?>
                <div class="wd-table">
                    <table cellspacing="0" cellpadding="0" class="display" id="table-list">
                        <thead>
                            <tr class="wd-filter-search">
                            <td><input type="hidden" /></td>
                            <?php
                            $number_of_fields = 0;
                            foreach ($view_fields as $key => $value) {
                                foreach ($value["ProjectDetail"] as $key1 => $value1) {
                                    foreach ($value1 as $field_name => $alias) {
                                        $number_of_fields++;
                                        switch ($field_name) {
                                            case "project_name":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "long_project_name":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_code_1":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter code" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_code_2":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter code" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "company_id":
                                                ?>
                                                <td>
                                                    <select name="search_company_name" class="seach_table_index">
                                                        <option value=""><?php echo __("--Select company---") ?></option>
                                                        <?php foreach ($search_company as $man) { ?>
                                                            <option><?php echo $man['company_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_manager_id":
                                                ?>
                                                <td>
                                                    <select name="search_manager" style="width:90%;" class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($search_manager as $man) { ?>
                                                            <option><?php echo $man['fullname']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_priority_id":
                                                ?>
                                                <td>
                                                    <select name="search_priority" style="width:90%;" class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($search_priority as $man) { ?>
                                                            <option><?php echo $man['priority']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_phase_id":
                                                ?>
                                                <td>
                                                    <select name="search_phase" style="width:90%;" class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($search_phase as $man) { ?>
                                                            <option><?php echo $man['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_status_id":
                                                ?>
                                                <td>
                                                    <select name="search_status" style="width:90%;"class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($search_status as $man) { ?>
                                                            <option><?php echo $man['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "start_date":
                                                ?>
                                                <td width="8%"><div class="wd-input wd-calendar-filter" style="width: 90%;"><input style="width: 90%;" type="text" name="startdate" id="startdate" value="" class="seach_table_index search_init" /></div></td>
                                                <?php
                                                break;
                                            case "end_date":
                                                ?>
                                                <td width="8%"><div class="wd-input wd-calendar-filter" style="width: 90%;"><input style="width: 90%;" type="text" name="enddate" id="enddate" value="" class="seach_table_index search_init" /></div></td>
                                                <?php
                                                break;
                                            case "planed_end_date":
                                                ?>
                                                <td width="10%"><div class="wd-input wd-calendar-filter" style="width: 90%;"><input style="width: 90%;" type="text" name="plannedenddate" id="plannedenddate" value="" class="seach_table_index search_init" /></div></td>
                                                <?php
                                                break;
                                            case "primary_objectives":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_objectives":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "issues":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "constraint":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "remark":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "created_value":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "complexity_id":
                                                ?>
                                                <td>
                                                </td>
                                                <?php
                                                break;
                                            case "project_type_id":
                                                ?>
                                                <td></td> 
                                                <?php
                                                break;
                                            case "project_sub_type_id":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_program_id":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_sub_program_id":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "chief_business_id":
                                                ?>
                                                <td> </td>
                                                <?php
                                                break;
                                            case "weather_amr":
                                                ?>
                                                <td></td> 
                                                <?php
                                                break;
                                            case "currency_id_amr":
                                                ?>
                                                <?php //if (!empty($project['ProjectAmr'])) {  ?>
                                                <td></td>
                                                <?php //}   ?>
                                                <?php
                                                break;
                                            case "currency_id":
                                                ?>
                                                <?php //if (!empty($project['ProjectAmr'])) {  ?>
                                                <td></td>
                                                <?php //}   ?>
                                                <?php
                                                break;
                                            case "budget":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter number" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_manager_id_amr":
                                                ?>
                                                <td>
                                                    <select name="search_manager" style="width:90%;" class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($search_manager as $man) { ?>
                                                            <option><?php echo $man['fullname']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_amr_program_id_amr":
                                                ?>
                                                <td>
                                                    <select name="search_amr_program" style="width:90%;" class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($amr_programs as $man) { ?>
                                                            <option><?php echo $man['ProjectAmrProgram']['amr_program']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_amr_sub_program_id_amr":
                                                ?>
                                                <td>
                                                    <select name="search_amr_program" style="width:90%;" class="seach_table_index">
                                                        <option value=""><?php echo __("-- Select --") ?></option>
                                                        <?php foreach ($amr_sub_programs as $man) { ?>
                                                            <option><?php echo $man ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <?php
                                                break;
                                            case "project_amr_category_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_sub_category_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "budget_amr":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_amr_status_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_mep_date_amr":
                                                ?>
                                                <td width="8%"><div class="wd-input wd-calendar-filter" style="width: 90%;"><input style="width: 90%;" type="text" name="projectamrmepdateamr" id="projectamrmepdateamr" value="" class="seach_table_index search_init" /></div></td>
                                                <?php
                                                break;
                                            case "project_amr_progression_amr":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter number" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_phases_id":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_cost_control_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_phases_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_organization_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_plan_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_perimeter_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_risk_control_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_problem_control_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "project_amr_risk_information_amr":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_amr_problem_information_amr":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_amr_solution_amr":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "project_amr_solution_description_amr":
                                                ?>
                                                <td><input type="text" name="search_name" value="Enter name" class="seach_table_index search_init" /></td>
                                                <?php
                                                break;
                                            case "cost_control_weather_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "planning_weather_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "risk_control_weather_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "organization_weather_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "perimeter_weather_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "issue_control_weather_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "md_validated_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "md_engaged_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "md_forecasted_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "md_variance_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "validated_currency_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "engaged_currency_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "forecasted_currency_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "variance_currency_id_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "validated_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "engaged_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "forecasted_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                            case "variance_amr":
                                                ?>
                                                <td></td>
                                                <?php
                                                break;
                                        }
                                    }
                                }
                            }
                            //echo $number_of_fields;
                            ?>                      
                            <td width="7%"><a href="javascript:void(0);" class="wd-reload"><?php echo __("Reload") ?></a></td>
                            </tr>
                            <tr class="wd-header">
                            <th><?php echo __('#', true); ?></th>
                            <?php
                            foreach ($view_fields as $key => $value) {
                                foreach ($value["ProjectDetail"] as $key1 => $value1) {
                                    foreach ($value1 as $field_name => $alias) {
                                        switch ($field_name) {
                                            case "project_name":
                                                ?>
                                                <th><?php echo __('Project name', true); ?></th>
                                                <?php
                                                break;
                                            case "long_project_name":
                                                ?>
                                                <th><?php echo __('Long project name', true); ?></th>
                                                <?php
                                                break;
                                            case "project_code_1":
                                                ?>
                                                <th><?php echo __('Project code 1', true); ?></th>
                                                <?php
                                                break;
                                            case "project_code_2":
                                                ?>
                                                <th><?php echo __('Project code 2', true); ?></th>
                                                <?php
                                                break;
                                            case "company_id":
                                                ?>
                                                <th><?php echo __('Company', true); ?></th>
                                                <?php
                                                break;
                                            case "project_manager_id":
                                                ?>
                                                <th><?php echo __("Project manager", true); ?></th>
                                                <?php
                                                break;
                                            case "project_priority_id":
                                                ?>
                                                <th><?php echo __("Priority", true); ?></th>
                                                <?php
                                                break;
                                            case "project_phase_id":
                                                ?>
                                                <th><?php echo __("Phase", true); ?></th>
                                                <?php
                                                break;
                                            case "project_status_id":
                                                ?>
                                                <th><?php echo __("Status", true); ?></th>
                                                <?php
                                                break;
                                            case "start_date":
                                                ?>
                                                <th width="5%"><?php echo __("Start date", true); ?></th>
                                                <?php
                                                break;
                                            case "end_date":
                                                ?>
                                                <th><?php echo __("End date", true); ?></th>
                                                <?php
                                                break;
                                            case "planed_end_date":
                                                ?>
                                                <th><?php echo __("Planed end date", true); ?></th>
                                                <?php
                                                break;
                                            case "primary_objectives":
                                                ?>
                                                <th><?php echo __("Primary Objectives", true); ?></th>
                                                <?php
                                                break;
                                            case "project_objectives":
                                                ?>
                                                <th><?php echo __("Project Objectives", true); ?></th>
                                                <?php
                                                break;
                                            case "issues":
                                                ?>
                                                <th><?php echo __("Issues", true); ?></th>
                                                <?php
                                                break;
                                            case "constraint":
                                                ?>
                                                <th><?php echo __("Constraint", true); ?></th>
                                                <?php
                                                break;
                                            case "remark":
                                                ?>
                                                <th><?php echo __("Remark", true); ?></th>
                                                <?php
                                                break;
                                            case "complexity_id":
                                                ?>
                                                <th><?php echo __("Complexity", true); ?></th>
                                                <?php
                                                break;
                                            case "created_value":
                                                ?>
                                                <th><?php echo __("Created Value", true); ?></th>
                                                <?php
                                                break;
                                            case "project_type_id":
                                                ?>
                                                <th><?php echo __("Type", true); ?></th>
                                                <?php
                                                break;
                                            case "project_sub_type_id":
                                                ?>
                                                <th><?php echo __("Sub Type", true); ?></th>
                                                <?php
                                                break;
                                            case "chief_business_id":
                                                ?>
                                                <th><?php echo __("Chief Business", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_program_id":
                                                ?>
                                                <th><?php echo __("Program", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_sub_program_id":
                                                ?>
                                                <th><?php echo __("Sub Program", true); ?></th>
                                                <?php
                                                break;
                                            case "weather_amr":
                                                ?>
                                                <?php //if (!empty($project['ProjectAmr'])) {  ?>
                                                <th><?php echo __("Weather", true); ?></th>
                                                <?php //}  ?>
                                                <?php
                                                break;
                                            case "currency_id_amr":
                                                ?>
                                                <?php //if (!empty($project['ProjectAmr'])) { ?>
                                                <th><?php echo __("Currency", true); ?></th>
                                                <?php //}  ?>
                                                <?php
                                                break;
                                            case "currency_id":
                                                ?>
                                                <?php //if (!empty($project['ProjectAmr'])) { ?>
                                                <th><?php echo __("Currency", true); ?></th>
                                                <?php //}   ?>
                                                <?php
                                                break;
                                            case "budget":
                                                ?>
                                                <th><?php echo __("Budget", true); ?></th>
                                                <?php
                                                break;
                                            case "project_manager_id_amr":
                                                ?>
                                                <th><?php echo __("Project manager", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_program_id_amr":
                                                ?>
                                                <th><?php echo __("Program", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_sub_program_id_amr":
                                                ?>
                                                <th><?php echo __("Sub Program", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_category_id_amr":
                                                ?>
                                                <th><?php echo __("Category", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_sub_category_id_amr":
                                                ?>
                                                <th><?php echo __("Sub Category", true); ?></th>
                                                <?php
                                                break;
                                            case "budget_amr":
                                                ?>
                                                <th><?php echo __("Budget", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_status_id_amr":
                                                ?>
                                                <th><?php echo __("AMR Status", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_mep_date_amr":
                                                ?>
                                                <th><?php echo __("MEP Date", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_progression_amr":
                                                ?>
                                                <th><?php echo __("Progression", true); ?></th>
                                                <?php
                                                break;
                                            case "project_phases_id_amr":
                                                ?>
                                                <th><?php echo __("Current Phase", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_cost_control_id_amr":
                                                ?>
                                                <th><?php echo __("Cost Control", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_organization_id_amr":
                                                ?>
                                                <th><?php echo __("Organization", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_plan_id_amr":
                                                ?>
                                                <th><?php echo __("Planning", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_perimeter_id_amr":
                                                ?>
                                                <th><?php echo __("Perimeter", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_risk_control_id_amr":
                                                ?>
                                                <th><?php echo __("Risk Control", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_problem_control_id_amr":
                                                ?>
                                                <th><?php echo __("Problem Control", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_risk_information_amr":
                                                ?>
                                                <th><?php echo __("Risk Information", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_problem_information_amr":
                                                ?>
                                                <th><?php echo __("Problem Information", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_solution_amr":
                                                ?>
                                                <th><?php echo __("Solution", true); ?></th>
                                                <?php
                                                break;
                                            case "project_amr_solution_description_amr":
                                                ?>
                                                <th><?php echo __("Solution Description", true); ?></th>
                                                <?php
                                                break;
                                            case "cost_control_weather_amr":
                                                ?>
                                                <th><?php echo __("Cost Control Weather", true); ?></th>
                                                <?php
                                                break;
                                            case "planning_weather_amr":
                                                ?>
                                                <th><?php echo __("Planning Weather", true); ?></th>
                                                <?php
                                                break;
                                            case "risk_control_weather_amr":
                                                ?>
                                                <th><?php echo __("Risk Control Weather", true); ?></th>
                                                <?php
                                                break;
                                            case "organization_weather_amr":
                                                ?>
                                                <th><?php echo __("Organization Weather", true); ?></th>
                                                <?php
                                                break;
                                            case "perimeter_weather_amr":
                                                ?>
                                                <th><?php echo __("Perimeter Weather", true); ?></th>
                                                <?php
                                                break;
                                            case "issue_control_weather_amr":
                                                ?>
                                                <th><?php echo __("Issue Control Weather", true); ?></th>
                                                <?php
                                                break;
                                            case "md_validated_amr":
                                                ?>
                                                <th><?php echo __("M.D Validated", true); ?></th>
                                                <?php
                                                break;
                                            case "md_engaged_amr":
                                                ?>
                                                <th><?php echo __("M.D Engaged", true); ?></th>
                                                <?php
                                                break;
                                            case "md_forecasted_amr":
                                                ?>
                                                <th><?php echo __("M.D Forecasted", true); ?></th>
                                                <?php
                                                break;
                                            case "md_variance_amr":
                                                ?>
                                                <th><?php echo __("M.D Variance", true); ?></th>
                                                <?php
                                                break;
                                            case "validated_currency_id_amr":
                                                ?>
                                                <th><?php echo __("Validated Currency", true); ?></th>
                                                <?php
                                                break;
                                            case "engaged_currency_id_amr":
                                                ?>
                                                <th><?php echo __("Engaged Currency", true); ?></th>
                                                <?php
                                                break;
                                            case "forecasted_currency_id_amr":
                                                ?>
                                                <th><?php echo __("Forecasted Currency", true); ?></th>
                                                <?php
                                                break;
                                            case "variance_currency_id_amr":
                                                ?>
                                                <th><?php echo __("Variance Currency", true); ?></th>
                                                <?php
                                                break;
                                            case "validated_amr":
                                                ?>
                                                <th><?php echo __("Validated", true); ?></th>
                                                <?php
                                                break;
                                            case "engaged_amr":
                                                ?>
                                                <th><?php echo __("Engaged", true); ?></th>
                                                <?php
                                                break;
                                            case "forecasted_amr":
                                                ?>
                                                <th><?php echo __("Forecasted", true); ?></th>
                                                <?php
                                                break;
                                            case "variance_amr":
                                                ?>
                                                <th><?php echo __("Variance", true); ?></th>
                                                <?php
                                                break;
                                        }
                                    }
                                }
                            }
                            ?>
                            <th><?php echo __("Actions", true); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($projects as $project): //debug($project);
                                $class = null;
                                $i++
                                ?>
                                <tr>
                                <td><?php echo $i; ?></td>
                                <?php
                                foreach ($view_fields as $key => $value) {
                                    foreach ($value["ProjectDetail"] as $key1 => $value1) { //debug($value1);
                                        foreach ($value1 as $field_name => $alias) {
                                            switch ($field_name) {
                                                case "project_name":
                                                    ?>
                                                    <td><?php echo $this->Html->link($project['Project']['project_name'], array('controller' => 'projects', 'action' => 'edit', $project['Project']['id'])); ?></td>
                                                    <?php
                                                    break;
                                                case "long_project_name":
                                                    ?>
                                                    <td><?php echo $this->Html->link($project['Project']['long_project_name'], array('controller' => 'projects', 'action' => 'edit', $project['Project']['id'])); ?></td>
                                                    <?php
                                                    break;
                                                case "project_code_1":
                                                    ?>
                                                    <td><?php echo $project['Project']['project_code_1']; ?></td> 
                                                    <?php
                                                    break;
                                                case "project_code_2":
                                                    ?>
                                                    <td><?php echo $project['Project']['project_code_2']; ?></td> 
                                                    <?php
                                                    break;
                                                case "company_id":
                                                    ?>
                                                    <td><?php echo $project['Company']['company_name']; ?></td> 
                                                    <?php
                                                    break;
                                                case "project_manager_id":
                                                    ?>
                                                    <td align="left">
                                                        <?php echo $project["Employee"]["fullname"]; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_priority_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectPriority']['priority']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_phase_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectPhase']['name']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_status_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectStatus']['name']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "start_date":
                                                    ?>
                                                    <td><?php echo $str_utility->convertToVNDate($project['Project']['start_date']); ?>&nbsp;</td>
                                                    <?php
                                                    break;
                                                case "end_date":
                                                    ?>
                                                    <td><?php echo $str_utility->convertToVNDate($project['Project']['end_date']); ?>&nbsp;</td>
                                                    <?php
                                                    break;
                                                case "planed_end_date":
                                                    ?>
                                                    <td><?php echo $str_utility->convertToVNDate($project['Project']['planed_end_date']); ?>&nbsp;</td>
                                                    <?php
                                                    break;
                                                case "primary_objectives":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['primary_objectives']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_objectives":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['project_objectives']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "issues":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['issues']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "constraint":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['constraint']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "remark":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['remark']; ?>
                                                    </td>
                                                    <?php
                                                    break;

                                                case "budget":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['budget']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "complexity_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectComplexity']['name']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "created_value":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Project']['created_value']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_type_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectType']['project_type']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_sub_type_id":
                                                    ?>
                                                    <td><?php echo $project['ProjectSubType']['project_sub_type']; ?></td>
                                                    <?php
                                                    break;
                                                case "project_amr_program_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectAmrProgram']['amr_program']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_sub_program_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['ProjectAmrSubProgram']['amr_sub_program']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "chief_business_id":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['Project']['chief_business_id'])) {
                                                            foreach ($employees as $employee) {
                                                                if ($employee['Employee']['id'] == $project['Project']['chief_business_id']) {
                                                                    echo $employee['Employee']['first_name'] . ' ' . $employee['Employee']['last_name'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "currency_id":
                                                    ?>
                                                    <td>
                                                        <?php echo $project['Currency']['sign_currency']; ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php if (!empty($project['ProjectAmr'])) { ?>
                                                            <center><img  src="<?php echo $html->url('/img/' . $project['ProjectAmr'][0]['weather'] . '.png') ?>"/></center>
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "currency_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['Currency']['sign_currency'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_manager_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['Employee']['fullname'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_program_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrProgram']['amr_program'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_sub_program_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrSubProgram']['amr_sub_program'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_category_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrCategory']['amr_category'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_sub_category_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrSubCategory']['amr_sub_category'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "budget_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['budget'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_status_id_amr":
                                                    ?>
                                                    <td> 
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrStatus']['amr_status'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_mep_date_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $str_utility->convertToVNDate($amr['ProjectAmr']['project_amr_mep_date']);
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_progression_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['project_amr_progression'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_phases_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectPhases']['name'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_cost_control_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrCostControl']['amr_cost_control'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_organization_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrOrganization']['amr_organization'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_plan_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrPlan']['amr_plan'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_perimeter_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrPerimeter']['amr_perimeter'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_risk_control_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrRiskControl']['amr_risk_control'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_problem_control_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmrProblemControl']['amr_problem_control'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_risk_information_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['project_amr_risk_information'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_problem_information_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['project_amr_problem_information'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_solution_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['project_amr_solution'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "project_amr_solution_description_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['project_amr_solution_description'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "cost_control_weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    ?>
                                                                    <center><img  src="<?php echo $html->url('/img/' . $amr['ProjectAmr']['cost_control_weather'] . '.png') ?>"/></center>
                                                                    <?php
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "planning_weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    ?>
                                                                    <center><img  src="<?php echo $html->url('/img/' . $amr['ProjectAmr']['planning_weather'] . '.png') ?>"/></center>
                                                                    <?php
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "risk_control_weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    ?>
                                                                    <center><img  src="<?php echo $html->url('/img/' . $amr['ProjectAmr']['risk_control_weather'] . '.png') ?>"/></center>
                                                                    <?php
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "organization_weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    ?>
                                                                    <center><img  src="<?php echo $html->url('/img/' . $amr['ProjectAmr']['organization_weather'] . '.png') ?>"/></center>
                                                                    <?php
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "perimeter_weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    ?>
                                                                    <center><img  src="<?php echo $html->url('/img/' . $amr['ProjectAmr']['perimeter_weather'] . '.png') ?>"/></center>
                                                                    <?php
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "issue_control_weather_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    ?>
                                                                    <center><img  src="<?php echo $html->url('/img/' . $amr['ProjectAmr']['issue_control_weather'] . '.png') ?>"/></center>
                                                                    <?php
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "md_validated_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['md_validated'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "md_engaged_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['md_engaged'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "md_forecasted_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['md_forecasted'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "md_variance_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['md_variance'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "validated_currency_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['Currency']['sign_currency'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "engaged_currency_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['Currency']['sign_currency'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "forecasted_currency_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['Currency']['sign_currency'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "variance_currency_id_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['Currency']['sign_currency'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "validated_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['validated'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "engaged_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['engaged'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "forecasted_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['forecasted'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                                case "variance_amr":
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (!empty($project['ProjectAmr'])) {
                                                            foreach ($amrs as $amr) {
                                                                if ($project['ProjectAmr'][0]['id'] == $amr['ProjectAmr']['id']) {
                                                                    echo $amr['ProjectAmr']['variance'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    break;
                                            }
                                        }
                                    }
                                }
                                ?>
                                <td class="wd-action" nowrap >
                                    <?php echo $this->Html->link(__('Edit', true), array('controller' => 'projects', 'action' => 'edit', $project['Project']['id']), array('class' => 'wd-edit')); ?>
                                    <div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('controller' => 'projects', 'action' => 'delete', $project['Project']['id'], $view_id), array('class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $project['Project']['project_name'])); ?>
                                    </div> 

                                </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<script>
    $(function(){
    
        /* table*/
        var oTable;

        /* Add the events etc before DataTables hides a column */
        //	$("thead input").keyup( function () {
        //		/* Filter on the column (the index) of this element */
        //		oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex( 
        //			oTable.fnSettings(), $("thead input").index(this) ));
        //	} );
        var  fnEscapeRegex = function ( sVal )
        {
            var acEscape = [ '/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\', '$', '^' ];
            var reReplace = new RegExp( '(\\' + acEscape.join('|\\') + ')', 'g' );
            return sVal.replace(reReplace, '\\$1');
        }
        var onSelectChange = function($element){
            var index = $('.seach_table_index').index($element) + 1;
            if(index <= 0){
                alert('Config incorrect!');
                return;
            }
            if($element.val()){
                var regex = '';
                if($element.is(':text')){
                    regex = $element.val(); 
                }else{
                    regex = '^' + $element.val()+'$'; 
                }
                oTable.fnFilter(regex, index, true, false ); 
            }else{
                oTable.fnFilter( '', index, true, false ); 
            }
        }
    
        $("thead input").change(function(){
            onSelectChange($(this));
        });
    
        $("thead select").change(function(){
            onSelectChange($(this));
        });
    	
        /*
         * Support functions to provide a little bit of 'user friendlyness' to the textboxes
         */

        $("thead input").focus( function () {
            if ( this.defaultValue == this.value )
            {
                this.value = "";
            }
        } );
	
        $("thead input").blur( function (i) {
            if ( this.value == "" )
            {
                this.value = this.defaultValue;
            }
        } );

        oTable = $('#table-list').dataTable( {
            "sScrollY": "400px",
            "sScrollX": "100%",
            "sScrollXInner": "<?php if ($number_of_fields <= 6) echo "99.9%"; else echo "170%" ?>",
            "bScrollCollapse": true,
            "sDom": 'R<"H"lfr>t<"F"ip>',
            "bJQueryUI": true,
            "bPaginate": false,
            "bInfo": false,
            "sPaginationType": "full_numbers",
            "aoColumnDefs": [
                { "sWidth": "3%", "aTargets": [ 0 ] }
            ]
        } );
    
        if ( oTable.length > 0 ) {
            oTable.fnAdjustColumnSizing();
        }
        /* table .end */
    
        $('#plannedenddate,#startdate,#enddate,#projectamrmepdateamr').datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy',
            onSelect        : function (d, t) {
                oTable.fnFilter( d );
            }
        });
    
  
        $(".wd-reload").click(function(){
            $('.seach_table_index').each(function(i){
                var $element = $(this);
                if($element.val()){
                    oTable.fnFilter('',i+1);
                }
                $element.val('');
            });
        });
    });
    
</script>