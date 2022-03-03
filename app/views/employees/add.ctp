<?php
echo $html->script('jquery.validation.min'); 
echo $html->script('jquery.multiSelect');
echo $html->css('jquery.multiSelect');
echo $html->css('projects');
echo $html->script('strength');
?>
<style>
    .strength_meter {
        margin: 5px 0 0 5px;
        display: inline-block;
    }
    .veryweak {
        color: #f00;
    }
    .weak {
        color: #f60;
    }
    .medium, .strong {
        color: #57a957;
    }
    .password-rule h4 {
        padding: 0 0 10px 0;
    }
    .password-rule ul {
        list-style: square inside !important;
    }
    .control_resource, .see_all_projects, .see_budget,.update_budget,.update_your_form,.sl_budget{
        clear: left;
        padding: 5px;
        margin-left: 6%;
        margin-right: 160px;
        width: 180px;
    }
    .sl_budget1 {
        width:222px  !important;
    }
    #control_resource, #see_all_projects, #see_budget,#update_budget,#update_your_form,#sl_budget {
        float: none;
        width: auto;
    }
    .project-manage-colleft {
        clear: both;
        padding: 5px;
        margin-left: 12%;
        float:left;
    }
    #create_a_project, #delete_a_project, #change_status_project, #can_communication, #can_see_forecast{
        width: auto;
    }
    .can_communication,
    .change_status_project, .can_see_forecast{
        margin-left: 51.6%;
    }
    .project-manage-colright {
      margin-top: 40px;
    }
    .div-table {
        display: table;
        margin: 5px 0 5px 135px;
    }
    .div-row {
        display: table-row;
    }
    .div-cell {
        display: table-cell;
        padding: 5px;
        vertical-align: top;
        text-align: center;
    }
    .div-cell-label {
        width: 280px;
        text-align: left;
    }
    .groupExternal{
        float: left;
        width: 23.4%;
        border: 3px solid #C7C7C7;
        padding: 7px;
        margin-left: -7px;
    }
    .noGroupExternal{
        float: left;
        width: 23.4%;
    }
    .wd-input-capacity-hour input{
        border-left: none !important;
        height: 17px;
        text-align: center;
    }
    .wd-capacity-hour label{
        border: 1px solid #ccc;
        border-right: none;
        background-color: #eee;
    }
    #webservice-options {
        clear: both;
        padding: 5px;
        margin-left: 38%;
        width: 48%;
        float: left;
        display: none;
    }
	.input.checkbox label{
		float: none;
	}
	.div-row .input.checkbox{
		display: inline;
	}
</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("New Employee", true); ?></h2>
                </div>
                <div class="wd-tab">
                    <ul class="wd-item">
                    </ul>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->Session->flash(); ?>
                            <?php
                            echo $this->Form->create('Employee', array('enctype' => 'multipart/form-data','autocomplete' => 'off', 'id' => 'EmployeeAddForm'));
                            App::import("vendor", "str_utility");
                            $str_utility = new str_utility();
                            ?>
                            <fieldset>
                                <div class="wd-scroll-form" style="height: auto;">
                                    <div class="wd-left-content">
                                        <div class="wd-input">
                                            <label for="project-name"><?php __("First Name") ?></label>
                                            <?php echo $this->Form->input('first_name', array('div' => false, 'label' => false, 'class' => 'form-error', 'style' => 'color: #000')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="project-manager"><?php __("Last name") ?></label>
                                            <?php echo $this->Form->input('last_name', array('div' => false, 'label' => false, 'class' => 'form-error', 'style' => 'color: #000')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="priority"><?php __("Email") ?></label>
                                            <?php echo $this->Form->input('email', array('div' => false, 'label' => false,'autocomplete' => 'off')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="status"><?php __("Password") ?></label>
                                            <input type="password" style="display: none;" id="password-breaker" name="password-breaker" />
                                            <?php echo $this->Form->input('password', array('div' => false, 'label' => false, 'rel' => 'no-history', 'autocomplete' => 'off', 'value' => $default_user_profile['password'])); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="status"><?php __("Confirm Password") ?></label>
                                            <?php echo $this->Form->input('confirm_password', array('div' => false, 'label' => false, 'type' => 'password', 'value' => $default_user_profile['password'])); ?>
                                        </div>
                                        <div class="wd-input wd-input-80">
                                            <label for="address"><?php __("Address") ?></label>
                                            <?php echo $this->Form->input('address', array('type' => 'text','div' => false, 'label' => false, 'style' => 'width: 23% !important')); ?>
                                            <label for="budget" style="width: 12.5%;"><?php __("Type of Contract") ?></label>
                                            <?php echo $this->Form->input('contract_type_id', array('empty' => __('-- Select --' , true),'options' => $contract_types, 'div' => false, 'label' => false, 'style' => 'width: 23% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="startdate"><?php __("Company") ?></label>
                                            <?php if (count($tree) > 1) { ?>
                                                <select name="data[Employee][company_id]" id="EmployeeCompanyId" style="width : 24%">
                                                    <option value=""><?php echo __("---Select Company---") ?></option>
                                                    <?php
                                                    foreach ($tree as $key => $value) {
                                                        echo "<option value='" . $key . "'>" . str_replace('->', '--', $value) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            <?php } else {
                                                ?>
                                                <select name="data[Employee][company_id]" id="EmployeeCompanyId" style="width : 24%">
                                                    <option value=""><?php echo __("---Select Company---") ?></option>
                                                    <?php
                                                    foreach ($tree as $key => $value) {
                                                        echo "<option selected value='" . $key . "'>" . str_replace('->', '--', $value) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            <?php } ?>
                                            <label for="budget" style="width: 13.5%;"><?php __("Role") ?></label>
                                            <select name="data[Employee][role_id]" id="EmployeeRoleId" style="width: 23%; color: #000" class="form-error">
                                                <option value=""><?php echo __("---Select role---") ?></option>
                                                <?php
                                                $roles['ws'] = __('Webservice', true);
                                                if(!empty($profile_name)){
                                                    foreach ($profile_name as $key => $value) {
                                                        $roles['profile_' . $key] = $value;
                                                    }
                                                }
                                                foreach ($roles as $key => $value) {
                                                    //dont allow the pm to select company admin role
                                                    if( $is_pm && ($key == 2 || $key == 'ws') )continue;
                                                    
													if($key == 3){
														echo "<option selected value='" . $key . "'>" . __($value, true) . "</option>";
													}else{
														echo "<option value='" . $key . "'>" . __($value, true) . "</option>";
													}
                                                }
                                                ?>
                                            </select>
                                            <div id="webservice-options">
                                                <label><?php echo __('IP') ?></label>
                                                <?php echo $this->Form->input('ws_ip', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'style' => 'width: 45%')
                                                )
                                                ?>
                                            </div>
                                        </div>
                                        <!-- ticket -->
                                        <?php if( $enableTicket ) : ?>
                                            <?php if( $enableTicket ) : ?>
                                            <div class="wd-input">
                                                <label><?php echo __('Ticket') ?></label>
                                                <?php
                                                echo $this->Form->input('ticket_profile_id', array(
                                                    'type' => 'select',
                                                    'div' => false,
                                                    'label' => false,
                                                    'style' => 'width: 61.5%',
                                                    "empty" => __("-- Select -- ", true),
                                                    "options" => $listTicketProfiles));
                                                ?>
                                            </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <!-- end -->
                                        <div class="wd-input">
                                            <?php
                                            $arr_new = array();
                                            $arr_tam = array();
                                            foreach ($profitCenters as $key => $value) {
                                                if (strtolower($value) == 'default') {
                                                    $arr_new[$key] = $value;
                                                } else {
                                                    $arr_tam[$key] = $value;
                                                }
                                            }
                                            ?>
                                            <label><?php echo __('Profit Center') ?></label>
                                            <?php
                                            echo $this->Form->input('profit_center_id', array(
                                                'type' => 'select',
                                                'div' => false,
                                                'label' => false,
                                                'style' => 'width: 61.5%',
                                                "options" => $arr_new + $arr_tam));
                                            ?>
                                        </div>
                                        <div class="wd-input">
                                            <label><?php echo __('Average Daily Rate') ?></label>
                                            <?php
                                            echo $this->Form->input('tjm', array(
                                                'type' => 'text',
                                                'div' => false,
                                                'label' => false,
												'value' => $default_user_profile['tjm']
											));
                                            ?>
                                        </div>
                                         <div class="wd-input">
                                            <div style="float: left; width: 144px;" id="gLableExternal">
                                                <label for="budget"><?php __("external") ?></label>
                                                <label for="external" style="margin-top: 7px; display: none;" class="enterpriseCompany"><?php __("External company") ?></label>
                                            </div>
                                            <div id="gExternal">
                                            <?php
                                                $opt = array(__('Internal', true), __('External', true));
                                                if($manage_multiple_resource){
                                                    $opt[] = __('Multiple resource', true);
                                                }
                                                echo $this->Form->input('external', array(
                                                    'options' => $opt,
                                                    'div' => false,
                                                    'label' => false,
                                                    'style' => 'width: 100% !important; color: #000',
                                                    'class' => 'form-error'
                                                ));
                                            ?>
                                                <?php echo $this->Form->input('external_id', array("class" => 'enterpriseCompany', "empty" => __("-- Select -- ", true), 'options' => $externals, 'div' => false, 'label' => false, 'style' => 'width: 100%; clear: both; margin-top:10px;display: none;')); ?>
                                            </div>
                                            <div id="actif">
                                                <label for="actif" style="width: 14%;"><?php __("Actif") ?></label>
                                                <?php echo $this->Form->input('actif', array('options' => array(__('Not Actif', true), __('Actif', true)), 'value' => 1, 'div' => false, 'label' => false, 'style' => 'width: 23.5%')); ?>
                                            </div>
                                            <div>
                                            <label id="label-external" style="width: 36%;clear: both;color:red;display: none;"><?php __("Please select external company") ?></label>
                                            </div>
                                        </div>
                                        <div class="wd-input">
                                            <label for=""><?php __d(sprintf($_domain, 'Resource'), "ID3") ?></label>
                                            <?php echo $this->Form->input('id3', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                            <label for="budget" style="width: 14%;"><?php __d(sprintf($_domain, 'Resource'), "ID4") ?></label>
                                            <?php echo $this->Form->input('id4', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for=""><?php __d(sprintf($_domain, 'Resource'), "ID5") ?></label>
                                            <?php echo $this->Form->input('id5', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                            <label for="budget" style="width: 14%;"><?php __d(sprintf($_domain, 'Resource'), "ID6") ?></label>
                                            <?php echo $this->Form->input('id6', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label><?php echo __('Capacity') ?>/<?php echo __('Year') ?></label>
                                            <?php
                                            echo $this->Form->input('capacity_by_year', array(
                                                'type' => 'text',
                                                'div' => false,
                                                'label' => false,
												'value' => $default_user_profile['capacity_by_year']
											));
                                            ?>
                                        </div>
                                        <?php if($manage_hour): ?>
                                            <div class="wd-input">
                                                <label><?php echo __('Capacity in hour/day') ?></label>
                                                <div class="wd-capacity-hour">
                                                    <div class="wd-input-capacity-hour">
                                                        <label style="width : 7%"><?php echo __('Hour:') ?></label>
                                                        <?php
                                                        echo $this->Form->input('capacity_in_hour', array(
                                                            'type' => 'text',
                                                            'div' => false,
                                                            'style' => 'width: 10%',
                                                            'value' => 0,
                                                            'label' => false));
                                                        ?>
                                                    </div>
                                                    <div class="wd-input-capacity-hour">
                                                        <label style="width : 7%"><?php echo __('Minute:') ?></label>
                                                        <?php
                                                        echo $this->Form->input('capacity_in_minute', array(
                                                            'type' => 'text',
                                                            'div' => false,
                                                            'value' => 0,
                                                            'style' => 'width: 10%',
                                                            'label' => false));
                                                        ?>
                                                    </div>
                                                    <div class="wd-input-capacity-hour">
                                                        <label style="width : 10%; max-height: 29px;"><?php echo __('Total minutes:') ?></label>
                                                        <?php
                                                        echo $this->Form->input('total_minutes', array(
                                                            'type' => 'text',
                                                            'div' => false,
                                                            'style' => 'width: 10%',
                                                            'value' => 0,
                                                            'label' => false));
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wd-right-content">
                                        <div class="wd-input">
                                            <label for="code_id"><?php __("ID") ?></label>
                                            <?php echo $this->Form->input('code_id', array('type' => 'text', 'div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                            <label for="budget" style="width: 13%;"><?php __d(sprintf($_domain, 'Resource'), "ID2") ?></label>
                                            <?php echo $this->Form->input('identifiant', array('type' => 'text', 'div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="budget"><?php __("City") ?></label>
                                            <?php echo $this->Form->input('city_id', array('div' => false, "options" => $cities, 'label' => false, 'style' => 'width: 23.5%')); ?>
                                            <label for="budget" style="width: 13.5%;"><?php __("Post code"); ?></label>
                                            <?php echo $this->Form->input('post_code', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="budget"><?php __("Country") ?></label>
                                            <?php echo $this->Form->input('country_id', array('div' => false, 'label' => false, 'style' => 'width: 23.5% !important')); ?>
                                        </div>
                                        <div class="div-table">
                                            <div class="div-row">
												<?php echo $this->Form->input('email_receive', array(
													// 'div'=>false,
													'label'=>__("Authorize z0 Gravity email", true),
													'style'=>'width: auto !important;vertical-align:middle;float:none;',
													'type' => 'checkbox',
													'checked' => ($default_user_profile['email_receive'] == "1")
												));?>
                                                
                                            </div>
                                            <div class="div-row">
												<?php echo $this->Form->input('activate_copy', array(
													// 'div'=>false,
													'label'=> __("Activate COPY in timesheet", true) ,
													'style'=>'width: auto !important;vertical-align:middle;float:none;',
													'type' => 'checkbox',
													'checked' => ($default_user_profile['activate_copy'] == "1")
												));?>
												<a href="javascript:void(0)" style="vertical-align: middle;" class="copy-timesheet" title="<?php __('Copy Forecast')?>"></a>
                                            </div>
                                            <div class="div-row">
												<?php echo $this->Form->input('is_enable_popup', array(
													// 'div'=>false,
													'label'=>__("Enable popup", true),
													'style'=>'width: auto !important;vertical-align:middle;float:none;',
													'type' => 'checkbox',
													'checked' => ($default_user_profile['is_enable_popup'] == "1")
												));?>
                                            </div>
                                            <div class="div-row">
												<?php echo $this->Form->input('auto_timesheet', array(
													// 'div'=>false,
													'label'=>__("Auto validate timesheet", true) ,
													'style'=>'width: auto !important;vertical-align:middle;float:none;',
													'type' => 'checkbox',
													'checked' => ($default_user_profile['auto_timesheet'] == "1")
												));?>
                                            </div>
                                            <div class="div-row">
												<?php echo $this->Form->input('auto_absence', array(
													// 'div'=>false,
													'label'=>__("Auto validate absence", true) ,
													'style'=>'width: auto !important;vertical-align:middle;float:none;',
													'type' => 'checkbox',
													'checked' => ($default_user_profile['auto_absence'] == "1")
												));?>
                                            </div>
                                            <div class="div-row">
												<?php echo $this->Form->input('auto_by_himself', array(
													// 'div'=>false,
													'label'=>__("Auto validate by himself", true) ,
													'style'=>'width: auto !important;vertical-align:middle;float:none;',
													'type' => 'checkbox',
													'checked' => ($default_user_profile['auto_by_himself'] == "1")
												));?>
                                            </div>
                                        </div>
                                        <div class="wd-input">
                                            <label for="originaldate"><?php __("Work phone") ?></label>
                                            <?php
                                            echo $this->Form->input('work_phone', array('div' => false, 'label' => false));
                                            ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="finality"><?php __("Mobile phone") ?></label>
                                            <?php echo $this->Form->input('mobile_phone', array('div' => false, 'label' => false)); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="finality"><?php __("Home phone") ?></label>
                                            <?php echo $this->Form->input('home_phone', array('div' => false, 'label' => false)); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="constraint"><?php __("Fax number") ?></label>
                                            <?php echo $this->Form->input('fax', array('div' => false, 'label' => false)); ?>
                                        </div>
                                        <div class="wd-input" id="skill">
                                            <label><?php echo __('Skill') ?></label>
                                            <div id="skillBox">
                                            <?php
                                            echo $this->Form->input('project_function_id', array(
                                                'type' => 'select',
                                                'div' => false,
                                                'label' => false,
                                                "empty" => __("-- Select -- ", true),
                                                "options" => $projectFunctions));
                                            ?>
                                            </div>
                                        </div>
                                        <?php if($activateProfile):?>
                                        <div class="wd-input">
                                            <label><?php echo __('Profile') ?></label>
                                            <?php
                                            echo $this->Form->input('profile_id', array(
                                                'type' => 'select',
                                                'div' => false,
                                                'label' => false,
                                                'style' => 'width: 61.5%',
                                                "empty" => __("-- Select -- ", true),
                                                "options" => $profiles));
                                            ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="wd-input">
                                            <label for="start_date"><?php __("Start date") ?></label>
                                            <?php echo $this->Form->input('start_date', array('type' => 'text', 'div' => false, 'label' => false, 'value' => date('d-m-Y',time()))); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="budget"><?php __("End date") ?></label>
                                            <?php echo $this->Form->input('end_date', array('type' => 'text', 'div' => false, 'label' => false)); ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="wd-submit">
                                    <button type="submit" class="btn-text btn-green" id="btnSave" />
                                        <img src="<?php echo $this->Html->url('/img/ui/blank-save.png') ?>" />
                                        <span><?php __('Save') ?></span>
                                    </button>
                                    <a href="" class="btn-text btn-red" id="reset">
                                        <img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
                                        <span><?php __('Reset'); ?></span>
                                    </a>
                                </div>
                            </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $validation->bind("Employee", array('form' => '#EmployeeAddForm')); ?>
<?php echo $html->script('validateDate');

$sl_budget = array(
	0 => __('Not Display Budget', true),
	1 => __('Readonly Budget', true),
	2 => __('Update Budget', true),
);?>
<script>
	var default_user_profile = <?php echo json_encode($default_user_profile);?>;
    $("select[name='data[Employee][project_function_id]']").multiSelect({noneSelected: 'Select skills', oneOrMoreSelected: '*', selectAll: false });
    function isNotEmpty(elementId,cpr){
        var date = $("#"+elementId).val();
        if(date==''){
            var endDate = $("#"+elementId);
            var parentElem = endDate.parent();
            parentElem.addClass("error");
            endDate.addClass("form-error");
            switch (elementId) {
                case "EmployeeRoleId":
                    parentElem.append('<div class="error-message2"><?php echo h(__("This field is not blank.", true)); ?></div>');
                    break;
                case "EmployeeCompanyId":
                    parentElem.append('<div class="error-message1"><?php echo h(__("This field is not blank.", true)); ?></div>');
                    break;
                case "EmployeeProfitCenterId":
                    parentElem.append('<div class="error-message"><?php echo h(__("This field is not blank.", true)); ?></div>');
                    break;
                default:
                    parentElem.append('<div class="error-message"><?php echo h(__("This field is not blank.", true)); ?></div>');
                }
                return false;
            }
            return true;
        }
        function reset_form(){
            removeMessage();
            $("input").val('');
            $("#EmployeeCompanyId").val('');
            $("#EmployeeRoleId").val('');
            $("#EmployeeCityId").attr({disabled:"true"});
            $("#EmployeeCountryId").attr({disabled:"true"});
            $("#EmployeeCityId").html("<option></option>");
            $("#EmployeeCountryId").html("<option></option>");
        }
        $('#btnSave').click(function(){
            removeMessage();
            var v1=false,v2=false,v3=false,v4=false,v5=true;
            v1=isNotEmpty('EmployeeCompanyId');
            v2=isNotEmpty('EmployeeRoleId');
            v3=isNotEmpty('EmployeeFirstName');
            v4=isNotEmpty('EmployeeLastName');
            v6=true;
            if (v3&&v4) {
                if ($("#EmployeePassword").val() != $("#EmployeeConfirmPassword").val()) {
                    var endDate = $("#EmployeePassword");
                    var parentElem = endDate.parent();
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("The password do not match.", true)); ?></div>');
                    endDate = $("#EmployeeConfirmPassword");
                    parentElem = endDate.parent();
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("The password do not match.", true)); ?></div>');
                    v3=v4=false;
                }
            }
            if($("#EmployeeStartDate").val()){
                var endDate = $("#EmployeeStartDate");
                var parentElem = endDate.parent();
                if(!isDate('EmployeeStartDate')){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("Invalid Date (Valid format is dd-mm-yyyy).", true)); ?></div>');
                    v5= false;
                }else if(isDate('EmployeeEndDate') && compareDate('EmployeeStartDate','EmployeeEndDate') > 0 ){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("The end date must be greater than start date.", true)); ?></div>');
                    v5= false;
                }
            }
            if(v5 && $("#EmployeeEndDate").val()){
                var endDate = $("#EmployeeEndDate");
                var parentElem = endDate.parent();
                if(!isDate('EmployeeEndDate')){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("Invalid Date (Valid format is dd-mm-yyyy).", true)); ?></div>');
                    v5= false;
                }else if(isDate('EmployeeStartDate') && compareDate('EmployeeStartDate','EmployeeEndDate') > 0 ){
                    parentElem.addClass("error");
                    endDate.addClass("form-error");
                    parentElem.append('<div class="error-message"><?php echo h(__("The end date must be greater than start date.", true)); ?></div>');
                    v5= false;
                }
            }
            <?php if( $security['SecuritySetting']['complex_password']): ?>
            var valid = $('#EmployeePassword').data('plugin_strength').valid;
            if( !valid ){
                var endDate = $("#EmployeePassword");
                var parentElem = endDate.parent();
                parentElem.addClass("error");
                endDate.addClass("form-error");
                return false;
            }
            <?php endif ?>
            return v1&&v2&&v3&&v4&&v5&&v6;
        });

        function removeMessage(){
            $("#flashMessage").hide();
            $('div.error-message').remove();
            $('div.error-message1').remove();
            $('div.error-message2').remove();
            $('div.error-message3').remove();
            $("div.wd-input,input,select").removeClass("form-error");
            check_phase_exist = false;
        }
        $("#EmployeeCompanyId").change(function(){
            var id = $(this).val();
            $("#EmployeeCityId").removeAttr('disabled');
            $("#EmployeeCountryId").removeAttr('disabled');
            $.ajax({
                url: '<?php echo $html->url('/employees/get_city/') ?>' + id,
                beforeSend: function() { $("#EmployeeCityId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#EmployeeCityId").html(data);
                }
            });
            $.ajax({
                url: '<?php echo $html->url('/employees/get_country/') ?>' + id,
                beforeSend: function() { $("#EmployeeCountryId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#EmployeeCountryId").html(data);
                }
            });
            $.ajax({
                url: '<?php echo $html->url('/employees/get_profit_center/') ?>' + id,
                beforeSend: function() { $("#EmployeeProfitCenterId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#EmployeeProfitCenterId").html(data);
                }
            });
            $.ajax({
                url: '<?php echo $html->url('/employees/get_skill/') ?>' + id,
                beforeSend: function() {
                    $('#skillBox').html('');
                },
                success: function(data) {
                    $('#skillBox').append(data);
                    $("select[name='data[Employee][project_function_id]']").multiSelect({noneSelected: 'Select skills', oneOrMoreSelected: '*', selectAll: false });
                    $('#EmployeeProjectFunctionId').find('span').text('<?php __('Select skills'); ?>');
                    $('#skillBox .multiSelectOptions label').each(function(){
                        if($(this).find('input').is(':checked')){
                            $(this).removeAttr('class');
                            $(this).find('input').removeAttr('checked');
                        }
                    });
                }
            });
        });

        $(document).ready(function () {
            $("#EmployeeStartDate, #EmployeeEndDate").datepicker({
                showOn          : 'button',
                buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
                buttonImageOnly : true,
                dateFormat      : 'dd-mm-yy'
            });
            var company_id = $("#EmployeeCompanyId").val();
            if(company_id=="") {
                $("#EmployeeCityId").html("<option></option>");
                $("#EmployeeCountryId").html("<option></option>");
            } else {
                $("#EmployeeCityId").removeAttr('disabled');
                $("#EmployeeCountryId").removeAttr('disabled');
                $.ajax({
                    url: '<?php echo $html->url('/employees/get_city/') ?>' + company_id,
                    beforeSend: function() { $("#EmployeeCityId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#EmployeeCityId").html(data);
                    }
                });
                $.ajax({
                    url: '<?php echo $html->url('/employees/get_country/') ?>' + company_id,
                    beforeSend: function() { $("#EmployeeCountryId").html("<option>Loading...</option>"); },
                    success: function(data) {
                        $("#EmployeeCountryId").html(data);
                    }
                });
            }
        });
        $('#EmployeeWorkPhone').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeWorkPhone').val());
            $('#EmployeeWorkPhone').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Work phone must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeMobilePhone').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeMobilePhone').val());
            $('#EmployeeMobilePhone').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Mobile phone must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeHomePhone').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeHomePhone').val());
            $('#EmployeeHomePhone').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeHomePhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Home phone must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeHomePhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeFax').change(function(){
            var rule = /^([0-9]*)$/;
            var x=$.trim($('#EmployeeFax').val());
            $('#EmployeeFax').val(x);
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeFax");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Fax number must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeeFax");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeePostCode').change(function(){
            var rule = /^([0-9.]*)$/;
            var x=$.trim($('#EmployeePostCode').val());
            $('#EmployeePostCode').val(x);
            $('div.error-message3').remove();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeePostCode");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message3">'+"<?php echo h(__("The Postcode must be a number ", true)) ?>"+'</div>');
            } else {
                var fomrerror = $("#EmployeePostCode");
                fomrerror.removeClass("form-error");
                $('div.error-message3').remove();
            }
        });
        $("#EmployeeCodeId").change(function(){
            var code_id = $.trim($(this).val());
            //code_id = '';
            if(code_id){
                $.ajax({
                    url:  '<?php echo $html->url('/employees/check_code_id/') ?>' + code_id,
                    beforeSend: function() {
                        $("#EmployeeCodeId").after("<span id='check' style='line-height:24px'><?php echo h(__('Checking...', true)) ?></span>");
                        $("#EmployeeCodeId").removeClass("form-error");
                        $("#EmployeeCodeId").parent().find(".error-message").remove();
                        $("#EmployeeCodeId .error").remove();
                    },
                    success: function(data) {
                        if(data){
                            var parentElem = $("#EmployeeCodeId").parent();
                            $("#EmployeeCodeId").addClass("form-error");
                            parentElem.addClass("error");
                            parentElem.append('<div class="error-message"><?php echo h(__('ID Already exists', true)) ?></div>');
                        }else{
                            $('#btnSave').removeAttr('disabled');
                        }
                        $("#check").remove();
                    }
                })
            }
        });
        $('#EmployeeActif').change(function(){
            var _sl = $(this).find('option:selected').val();
            if(_sl == 0){
                var currentDate = <?php echo json_encode(date('d-m-Y', time()));?>;
                $('#EmployeeEndDate').val(currentDate);
            }
        });

<?php
if( $security['SecuritySetting']['complex_password'] ):
    $rules = array(
        sprintf(__('Minimum characters of %s', true), $security['SecuritySetting']['password_min_length'])
    );
    if( $security['SecuritySetting']['password_special_characters'] ){
        $messages = array_fill(0, 3, '<img src="'.$html->url('/img/test-fail-icon.png').'" alt="">');
        $messages[] = '<img src="'.$html->url('/img/test-pass-icon.png').'" alt="">';
        $rules[] = __('Uppercase letters (A-Z)', true);
        $rules[] = __('Lowercase letters (a-z)', true);
        $rules[] = __('Base 10 digits (0-9)', true);
        $rules[] = __('Non-alphanumeric characters (for example, !, $, #, %)', true);
        $valid = array(0, 0, 0, 1);
    } else {
        $messages = array_fill(0, 4, '<img src="'.$html->url('/img/test-pass-icon.png').'" alt="">');
        $valid = array(1, 1, 1, 1);
    }
?>
    //init complexify
    var el = $('#EmployeePassword').strength({
        minLength : <?php echo $security['SecuritySetting']['password_min_length'] ?>,
        text : <?php echo json_encode($messages) ?>,
        valid : <?php echo json_encode($valid) ?>,
        textPrefix : '',
        lengthError : '<?php printf(__('At least %s characters', true), $security['SecuritySetting']['password_min_length']) ?>',
<?php
    if( $security['SecuritySetting']['password_ban_list'] ):
        $rule = __('Password should not contain the user&#39;s first or last name', true);
        $rules[] = $rule;
?>
        banListError : '<?php echo $rule ?>',
        banList : function(){
            var list = [],
                fn = $('#EmployeeFirstName').val(),
                ln = $('#EmployeeLastName').val();
            if( fn.length )list.push(fn);
            if( ln.length )list.push(ln);
            return list;
        },
<?php endif ?>
        validWhenEmpty : true,
        emptyError: '<?php printf(__('At least %s characters', true), $security['SecuritySetting']['password_min_length']) ?>'
    });
    var _tit = <?php echo json_encode(__('Password rules', true));?>;
    el.tooltip({
        maxHeight : 500,
        maxWidth : 400,
        type : ['top','left'],
        content: '<div class="password-rule"><h4>' + _tit + '</h4><ul><li>- ' + (<?php echo json_encode($rules) ?>).join('</li><li>- ') + '</li></ul></div>'
    });
<?php
endif;
?>
    var adminSeeAllProjects = <?php echo json_encode($adminSeeAllProjects);?>;
    var EPM_see_the_budget = <?php echo json_encode($EPM_see_the_budget);?>;
    var list_profile_name = <?php echo json_encode($list_profile_name) ?>;
    var can_control_resource = function(){
        if( $('#EmployeeRoleId').val() != 2 && $('#EmployeeRoleId').val() != 4 && $('#EmployeeRoleId').val() != 'ws'){
            $('.project-manage-colright,.project-manage-colleft').show();
            $('.see_all_projects').hide();
            $('.see_budget').hide();
            $('.update_budget').hide();
            $('.sl_budget').hide();
            $('.control_resource').show();
            if(!adminSeeAllProjects){
                $('.see_all_projects').show();
            }
            if(EPM_see_the_budget){
                $('.see_budget').show();
                $('.update_budget').show();
                $('.sl_budget').show();
            }
            $('.update_your_form').show();
            $('.create_a_project').show();
            $('.delete_a_project').show();
            <?php if( !empty($enabled_menus['communications'])){?>
				$('.can_communication').show();
			<?php }else{ ?> 
				$('.can_communication').hide();
			<?php } ?> 
            <?php if(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast']){?>
				$('.can_see_forecast').show();
			<?php }else{ ?> 
				$('.can_see_forecast').hide();
			<?php } ?> 
			// neu dang chon role Profile Project Manager thi moi chay cac dong duoi.
            var tempId = $('#EmployeeRoleId').val();
            var _tempId = tempId.split("_");
            if(_tempId[0] && _tempId[1] && _tempId[0] == 'profile'){
                $('.see_budget').hide();
                $('.update_budget').hide();
                $('.sl_budget').hide();
                // $('.update_your_form').hide();
                if(list_profile_name[_tempId[1]]){
                    var value = list_profile_name[_tempId[1]];
					// kiem tra setting trong admin.
                    if(value['can_change_status_project'] && value['can_change_status_project'] == 1){
                        $('#change_status_project').prop('checked', true);
                    } else {
                        $('#change_status_project').prop('checked', false);
                    }
                    if(value['can_create_project'] && value['can_create_project'] == 1){
                        $('#create_a_project').prop('checked', true);
                    } else {
                        $('#create_a_project').prop('checked', false);
                    }
                    if(value['can_delete_project'] && value['can_delete_project'] == 1){
                        $('#delete_a_project').prop('checked', true);
                    } else {
                        $('#delete_a_project').prop('checked', false);
                    }
                    if(value['can_communication'] && value['can_communication'] == 1){
                        $('#can_communication').prop('checked', true);
                    } else {
                        $('#can_communication').prop('checked', false);
                    }
                    if(value['create_resource'] && value['create_resource'] == 1){
                        $('#control_resource').prop('checked', true);
                    } else {
                        $('#control_resource').prop('checked', false);
                    }
					//them dieu kien de uncheck khi tao user voi role Profile Project Manager.
					if(!value['update_your_form']){
						$('#update_your_form').prop('checked', false);
						$('.update_your_form').hide();
					}
                }
            }
        } else {
            $('.project-manage-colright,.project-manage-colleft').hide();
        }
        if( $('#EmployeeRoleId').val() == 'ws' ){
            $('#webservice-options').show();
            setTimeout(function(){
                $('#EmployeeWsIp').focus();
            }, 250);
        } else {
            $('#webservice-options').hide();
        }
    }
	//set value defauft cho cac check-box.
    $('<div class="project-manage-colleft"><div class="control_resource"><input type="hidden" name="data[control_resource]" value="0" /><input type="checkbox" name="data[control_resource]" value="1" id="control_resource" ' +  ((default_user_profile.control_resource == "1") ? 'checked="checked"' : '') +' /><?php __('Allow managing resources?') ?></div>').insertAfter('#EmployeeRoleId');
	
    $('<div class="see_all_projects"> <input type="hidden" name="data[see_all_projects]" value="0" /><input type="checkbox" name="data[see_all_projects]" value="1" id="see_all_projects" /><?php __('See All Project') ?></div>').insertAfter('.control_resource');
    $('<div class="sl_budget">' + <?php echo json_encode($this->Form->input("sl_budget", array(
		"type" => "select",
		"name" => "data[sl_budget]",
		"id" => "sl_budget",
		"class" => "sl_budget1",
		"div" => false,
		"label" => false,
		"empty" => false,
		"options" => $sl_budget,
		"selected" => !empty($default_user_profile["sl_budget"]) ? $default_user_profile["sl_budget"] : 0,
	)));?> + '</div>').insertAfter('.see_all_projects');
	
    $('<div class="update_your_form"><input type="hidden" name="data[update_your_form]" value="0" /><input type="checkbox" name="data[update_your_form]" value="1" id="update_your_form" ' +  ((default_user_profile.update_your_form == "1") ? 'checked="checked"' : '') +'/><?php __('Update Your form') ?></div></div>').insertAfter('.sl_budget');
	
    $('<div class="project-manage-colright"><div class="create_a_project"><input type="hidden" name="data[create_a_project]" value="0" /><input type="checkbox" name="data[create_a_project]" value="1" id="create_a_project" ' +  ((default_user_profile.create_a_project == "1") ? 'checked="checked"' : '') +' /><?php __('Create a project') ?></div></div>').insertAfter('.project-manage-colleft');
	
    $('<br><div style="margin-left: 51.6%" class="delete_a_project"><input type="hidden" name="data[delete_a_project]" value="0" /><input type="checkbox" name="data[delete_a_project]" value="1" id="delete_a_project" ' +  ((default_user_profile.delete_a_project == "1") ? 'checked="checked"' : '') +'/><?php __('Delete a project') ?></div></div>').insertAfter('.create_a_project');
	
    $('<br><div class="change_status_project"><input type="hidden" name="data[change_status_project]" value="0" /><input type="checkbox" name="data[change_status_project]" value="1" id="change_status_project" ' +  ((default_user_profile.change_status_project == "1") ? 'checked="checked"' : '') +'/><?php __('Can change the status opportunity/in progress') ?></div></div>').insertAfter('.delete_a_project');
	
    $('<br><div style="margin-left: 51.6%" class="can_communication"><input type="hidden" name="data[can_communication]" value="0" /><input type="checkbox" name="data[can_communication]" value="1" id="can_communication" ' +  ((default_user_profile.can_communication == "1") ? 'checked="checked"' : '') +' /><?php __('Can communicate communication') ?></div></div>').insertAfter('.change_status_project');
	 $('<br><div class="can_see_forecast"><input type="hidden" name="data[can_see_forecast]" value="0" /><input type="checkbox" name="data[can_see_forecast]" value="1" id="can_see_forecast" ' +  ((default_user_profile.can_see_forecast == "1") ? 'checked="checked"' : '') +'/><?php __('Can see the forecast') ?></div></div>').insertAfter('.can_communication');
	 
    $('#EmployeeRoleId').change(can_control_resource);
    can_control_resource();
    /**
     * Show/Hide Enterprise Company
     */
    function showHideEnterprise(val){
        if(val == 1){
            $('.enterpriseCompany').show();
            $('#gExternal').addClass('groupExternal');
            $('#gExternal').removeClass('noGroupExternal');
            $('#actif label').css('width', '12%');
            $('#gLableExternal label:first-child').css('margin-top', '10px');
        } else {
            $('.enterpriseCompany').hide();
            $('#gExternal').addClass('noGroupExternal');
            $('#gExternal').removeClass('groupExternal');
            $('#actif label').css('width', '14%');
            $('#gLableExternal label:first-child').css('margin-top', '0');
        }
    }
    showHideEnterprise($('#EmployeeExternal').val());
    $('#EmployeeExternal').change(function(){
        var val = $(this).val();
        showHideEnterprise(val);
    });
    $('#EmployeeExternal').click(function(){
        if($('#EmployeeExternal').val() == 2){
            $('#multiResource').show();
        } else {
            $('#multiResource').hide();
        }
        if($('#EmployeeExternal').val() == 1){
            if( $('#EmployeeExternalId').val() == ''){
                $('#label-external').show();
                $('#btnSave').hide();
            }
            $('#label-external').show();
            $('#EmployeeExternalId').click(function(){
                if( $('#EmployeeExternalId').val() == ''){
                    $('#label-external').show();
                    $('#btnSave').hide();
                } else {
                    $('#label-external').hide();
                    $('#btnSave').show();
                }
            });
        } else {
            $('#label-external').hide();
        }
        if($('#EmployeeExternal').val() != 1){
            $('#label-external').hide();
            $('#btnSave').show();
        }
    });
    $('#EmployeeCapacityInHour, #EmployeeCapacityInMinute').keyup(function(){
        var val1 = $('#EmployeeCapacityInHour').val() ? $('#EmployeeCapacityInHour').val() : 0,
            val2 = $('#EmployeeCapacityInMinute').val() ? $('#EmployeeCapacityInMinute').val() : 0,
            val = parseFloat(parseFloat(val1*60) + parseFloat(val2));
        $('#EmployeeTotalMinutes').val(val);
    });
    var fill_more_than_capacity_day = <?php echo json_encode($fill_more_than_capacity_day) ?>;
    $('#EmployeeCapacityInHour').keypress(function(e){
        var key = e.keyCode ? e.keyCode : e.which;
        if(!key || key == 8 || key == 13){return;}
        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
        var _val = parseFloat(val, 10);
        if(fill_more_than_capacity_day){
            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[0-9]{2})?$/.test(val) || !(_val >= 0 && _val <= 8))){
                e.preventDefault();
                return false;
            }
        } else {
            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[0-9]{2})?$/.test(val) || !(_val >= 0 && _val <= 24))){
                e.preventDefault();
                return false;
            }
        }
    });
    $('#EmployeeCapacityInMinute').keypress(function(e){
        var key = e.keyCode ? e.keyCode : e.which;
        if(!key || key == 8 || key == 13){return;}
        var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
        var _val = parseFloat(val, 10);
        var val0 = $('#EmployeeCapacityInHour').val(),
            val1 = fill_more_than_capacity_day ? 8 : 24;
        if( val0 == val1 ){
            return false;
        } else {
            if(!(val != '0' && val == '+') && (!/^[\-+]?([0-9]{1}|[0-9]{2})?$/.test(val) || !(_val >= 0 && _val <= 59))){
                e.preventDefault();
                return false;
            }
        }
    });
    $('#EmployeeCapacityInHour').blur(function(){
        var val0 = $('#EmployeeCapacityInHour').val(),
            val1 = fill_more_than_capacity_day ? 8 : 24;
        if(val0 == val1){
            $('#EmployeeCapacityInMinute').val('0');
        }
    });
    $('#EmployeeTotalMinutes').prop( "disabled", true );
	/* Auto comlete for email */
	$(window).ready(function(){
		$('.wd-content').find('input').trigger('change');
		$('#EmployeeDefault').addClass('ready');
	});
	$('#EmployeeFirstName, #EmployeeLastName').on('change keyup', function(){
		if( $('#EmployeeEmail').hasClass('edited') ) return;
		var email = '';
		var first_name = $('#EmployeeFirstName').val();
		email += ( default_user_profile.first_name == "1") ? first_name : '';
		var first_letter = first_name ? first_name[0] : '';
		email += ( default_user_profile.first_letter_first_name == "1") ? first_letter : '';
		email += default_user_profile.sperator;
		var last_name = $('#EmployeeLastName').val();
		email += ( default_user_profile.last_name == "1") ? last_name : '';
		first_letter = last_name ? last_name[0] : '';
		email += ( default_user_profile.first_letter_last_name == "1") ? first_letter : '';
		email += default_user_profile.domain_name;
		$('#EmployeeEmail').val(email).trigger('change');
		
	});
	/* END Auto comlete for email */
</script>
