<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('strength'); ?>
<?php echo $html->css(array('context/jquery.contextmenu')); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php echo $html->script('jquery.form'); ?>
<?php echo $html->script('jquery.ui.touch-punch.min'); ?>
<style type="text/css">
    #absence-fixed thead tr th{height: 15px;text-align: center;vertical-align: middle;}
    #absence-fixed th,#absence-fixed td.st{
        border-right : 1px solid #185790;
        color: #fff !important;
        text-align: left;
    }
    #absence-fixed .st a{
        color: #fff;
    }
    #absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    #absence-fixed input{
        width: 100%;text-align: center;vertical-align: middle; border: 1px; height: 22px;
    }
    .wd-title {padding-left: 10px;}
    #absence-scroll {
        overflow-x: scroll;
    }
    .ch-absen-validation{
        background-color: #F0F0F0;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-table input{
        width: 100%;text-align: right;vertical-align: middle; border: 1px; height: 22px;
    }
    #absence tbody td.ct{
        background-color: #DDD !important;
    }
    #absence-table tr td.ch-absen-validation{height: 18px; text-align: center;vertical-align: middle; background-color: #c3dd8c;}
    #absence-wrapper #absence-fixed{ width: 12% !important;}
    .context-menu-item-inner span{color: red;}
    #error{ color:#F00; padding-top:10px; font-size: 12px; }
    .mes_absence_atch{
        color: black;
        font-size: 15px;
        margin: 5px 0;
        font-weight: bold;
    }
    .ui-dialog .ui-dialog-titlebar-close { display:none; }
    .tab-panel {
            width: 100%;
            display: inline-block;
        }

        .tab-link:after {
            display: block;
            padding-top: 20px;
            content: '';
        }

        .tab-link li {
            margin: 0px 5px;
            float: left;
            list-style: none;
        }

        .tab-link a {
            padding: 3px 30px;
            display: inline-block;
            border-radius: 10px 3px 0px 0px;
            background: #8dc8f5;
            font-weight: bold;
            color: #4c4c4c;
        }
        .tab-link a:hover {
            background: #b7c7e5;
            text-decoration: none;
        }
        li.active a, li.active a:hover {
            background: #ccc;
            color: #0094ff;
            text-decoration:none;
        }
        .content-area {
            border-radius: 3px;
            box-shadow: -1px 1px 1px rgba(0,0,0,0.15);
            background: #fff;
            border: 1px solid #ccc;
        }
        .inactive{
            display: none;
        }
        .active {
            display: block;
        }
        .wd-submit{
            text-align: center;
        }
    #multiResource {
        display: none;
    }
    #show-value {
        padding: 5px;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 300px;
    }
    #show-absence {
        padding: 5px;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 300px;
    }
    #title-absences {
        font-size: 12px;
        background-color: #004483;
        padding: 10px;
        font-size:12px;
        display: none;
        border-radius: 5px;
        margin-top: 5px;
    }
    .h1-value {
        font-size: 16px;
        padding: 5px;
    }
    .text-value {
        padding: 5px;
    }
    .totalOfYear {
        clear: both;
        margin-top: 10px;
        margin-left: 23px;
        margin-bottom: -25px;
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
    .ok-new-project, .cancel-new-project{
        width: 90px;
        border: none;
        height: 27px;
        border-radius: 5px;
    }
    .ok-new-project:hover, .cancel-new-project:hover{
        cursor: pointer;
    }
    #btnNoAL{
        margin-left: 190px;
    }
    #modal_dialog_alert{
        min-height: 60px !important;
    }
    #modal_dialog_confirm{
        min-height: 40px !important;
        margin-left: 50px;
    }
    .title{
        color: #000;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .heading-back{
        display: none;
    }
    .wd-submit .btn-text{
       background: #538FFA url('/img/ui/blank-save.png') center no-repeat;
    }
    .wd-submit #reset,
    .wd-submit #reset_button{
        background: #e55c5c url('/img/ui/blank-reset.png') center no-repeat;
    }
</style>
<?php
    if($this->data['Employee']['update_budget'] == 1 && $see_budget == 1) {
        $sl_budget = 2;
    }
    elseif($this->data['Employee']['update_budget'] == 0 && $see_budget == 1) {
        $sl_budget = 1;
    } else {
        $sl_budget = 0;
    }
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

    #edit_avatar_employee{
        background-image: url('<?php echo $this->Html->webroot('img/front/camera.png'); ?>');
        padding: 9px;
        display: block;
        width: 7px;
        margin: 7px 35px;
    }
    #ch_avatar{
        position:absolute;
        top: 7px;
        left: 90%;
        margin-left:-88px;
    }
    #ch_avatar img{
        width: 100px;
        height: 116px;
    }
    .ch-edit{
        position: absolute;
        background-color: rgb(245, 245, 245);
        width: 100px;
        height: 32px;
        opacity: 0.6;
        display: none;
        top: 84px;
        /*left: 430px;*/
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
    .change_status_project{
        margin-left: 51.6%;
    }
    .can_communication, .can_see_forecast{
        margin-left: 51.6%;
    }
    .project-manage-colright {
      margin-top: 40px;
    }
    .wd-title span {
        font-size: 14px;
        color: #f60;
        display: inline-block;
        margin-left: 5px;
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
    #list-dialog {
        display: none;
        color: #000;
        font-size: 12px;
    }
    #list-dialog h3 {
        border-bottom: 1px solid #ddd;
        margin: 5px 10px;
    }
    #list-dialog ul {
        list-style: square !important;
        padding: 0;
    }
    #list-dialog ul li {
        padding: 2px 10px;
        margin-left: 20px;
    }
    .itemx,
    .itemx:link,
    .itemx:visited,
    .itemx:active,
    .itemx:hover {
        color: #013d74;
    }
    .wd-save-project {
        float: left;
        background-image: url(/img/front/bg-save-project.png);
    }
    .wd-save-project span {
        background-image: url(/img/front/bg-add-project-right.png);
    }
    #webservice-options {
        clear: both;
        padding: 5px;
        margin-left: 38%;
        width: 48%;
        float: left;
        display: none;
    }
</style>
<?php
$abc = array();
$def = array();
if (!empty($this->data['ProjectEmployeeProfitFunctionRefer'])) {
    foreach($this->data['ProjectEmployeeProfitFunctionRefer'] as $v){
        $abc[] = $v['function_id'];
        if( isset($projectFunctions[ $v['function_id'] ]) )
            $def[] = $projectFunctions[ $v['function_id'] ];
    }
}
?>
<div id="list-dialog" title="<?php __('List') ?>">
<?php
if( isset($projects) ){
?>
    <h3><?php __('Projects') ?></h3>
    <ul>
<?php
    foreach($projects as $p){
?>
    <li><a class="itemx" href="<?php echo $this->Html->url('/project_tasks/index/' . $p['P']['id']) ?>" target="_blank"><?php echo $p['P']['project_name'] ?></a></li>
<?php
    }
?>
    </ul>
<?php
}
if( isset($activities) ){
?>
    <h3><?php __('Activities') ?></h3>
    <ul>
<?php
    foreach($activities as $p){
?>
    <li><a class="itemx" href="<?php echo $this->Html->url('/activity_tasks/index/' . $p['P']['id']) ?>" target="_blank"><?php echo $p['P']['name'] ?></a></li>
<?php
    }
?>
    </ul>
<?php
}
?>
</div>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content wd-user-edit">
            <div class="wd-list-project">
                <div class="wd-title">
                    <div class="heading-back"><a href="javascript:window.history.back();" ><i class="icon-arrow-left"></i><span><?php __('Back');?></span></a></div>
                    <h2 class="wd-t1">
                        <?php echo __("Employee Detail", true); ?>
                        <?php if( $readOnly ): ?><span><?php echo __('(Read only)') ?></span><?php endif ?>
                    </h2>
                </div>
                <div class="wd-tab">
                    <ul class="wd-item"></ul>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo sprintf($this->Session->flash(), '<a href="javascript:;" onclick="$(\'#list-dialog\').dialog(\'open\');">Project/activity list</a>.'); ?>
                            <?php
                            echo $this->Form->create('Employee', array('enctype' => 'multipart/form-data', 'id' => 'EmployeeEditForm'));
                            echo $this->Form->hidden('readOnly', array('value' => $readOnly));
                            echo $validation->bind("Employee", array('form' => '#EmployeeEditForm')); 
                            echo $this->Form->input('id');
                            App::import("vendor", "str_utility");
                            $str_utility = new str_utility();
                            foreach (array('start_date', 'end_date') as $d) {
                                if (isset($this->data['Employee'][$d]) && $this->data['Employee'][$d] == '0000-00-00') {
                                    $this->Form->data['Employee'][$d] = '';
                                } elseif (!empty($this->data['Employee'][$d])) {
                                    $this->Form->data['Employee'][$d] = date('d-m-Y', strtotime($this->data['Employee'][$d]));
                                }
                            }
                            ?>
                            <fieldset>
                                <div class="wd-scroll-form" style="height: auto;">
                                    <div class="wd-left-content" style="position: relative;">
                                        <div class="wd-input" >
                                            <label for="project-name"><?php __("First Name"); ?></label>
                                            <?php echo $this->Form->input('first_name', array('div' => false, 'label' => false, 'style' => 'width: 43%;color: #000', 'class' => 'form-error')); ?>
                                            <div id="ch_avatar">
                                                <?php
                                                $linkAvatar = $this->UserFile->avatar($id,'avatar');
                                                ?>
                                                <img src="<?php echo $linkAvatar;?>" />
                                                <?php if( !$readOnly ): ?>
                                                <div class="ch-edit">
                                                    <a class="wd-edit" href="javascript:void(0);" id="edit_avatar_employee"></a>
                                                </div>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                        <div class="wd-input" >
                                            <label for="project-manager"><?php __("Last name"); ?></label>
                                            <?php echo $this->Form->input('last_name', array('div' => false, 'label' => false, 'style' => 'width: 43%;color: #000', 'class' => 'form-error')); ?>
                                        </div>
                                        <div class="wd-input" >
                                            <label for="priority"><?php __("Email"); ?></label>
                                            <?php echo $this->Form->input('email', array('div' => false, 'label' => false, 'style' => 'width: 43%;', 'autocomplete' => 'off')); ?>
                                        </div>

                                        <div class="wd-input">
                                            <label for="status"><?php __("Password"); ?></label>
                                            <input type="password" style="display: none;" id="password-breaker" name="password-breaker" />
                                            <?php echo $this->Form->input('password', array('div' => false, 'label' => false, "value" => "")); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="status"><?php __("Confirm Password") ?></label>
                                            <?php echo $this->Form->input('confirm_password', array('div' => false, 'label' => false, 'type' => 'password')); ?>
                                        </div>
                                        <div class="wd-input wd-input-80">
                                            <label for="code_id"><?php __("Address") ?></label>
                                            <?php echo $this->Form->input('address', array('type' => 'text', 'div' => false, 'label' => false, 'style' => 'width: 23% !important')); ?>
                                            <label for="budget" style="width: 12.5%; padding-top: 0"><?php __("Type of Contract") ?></label>
                                            <?php echo $this->Form->input('contract_type_id', array('empty' => __('-- Select --', true), 'options' => $contract_types, 'div' => false, 'label' => false, 'style' => 'width: 23% !important')); ?>
                                        </div>
                                        <div class="wd-input">

                                            <label for="current-phase"><?php echo __("Company"); ?></label>
                                            <select name="data[Employee][company_id]" id="EmployeeCompanyId" style="width : 24%" >
                                                    <!--<option value=""><?php echo __("---Select Company---") ?></option> -->
                                                <?php
                                                foreach ($tree as $key => $value) {
                                                    if ($company_id == $key) {
                                                        echo "<option selected value='" . $key . "'>" . str_replace('->', '', $value) . "</option>";
                                                        break;
                                                    } else if ((isset($isclear_db)) && ($isclear_db)) {
                                                        echo "<option value='" . $key . "'>" . str_replace('->', '', $value) . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <label for="current-phase" style="width:13.5%"><?php __("Role") ?></label>
                                            <?php
                                            $roles['ws'] = __('Webservice', true);
                                            if(!empty($profile_name)){
                                                foreach ($profile_name as $key => $value) {
                                                    $roles['profile_' . $key] = $value;
                                                }
                                            }
                                            if($employee_info['Employee']['id'] == $id && $employeeLogin['Employee']['is_sas'] == 0){
                                                foreach ($roles as $key => $value) {
                                                    //dont allow the pm to select company admin role
                                                    if(!empty($this->data['Employee']['profile_account']) && 'profile_' . $this->data['Employee']['profile_account'] == $key){
                                                        echo "<span style='margin-top:6px; position:absolute'> : &nbsp; " . __($value, true) . "</span>";
                                                        continue;
                                                    }
                                                    if( $is_pm && ($key == 2 || $key == 'ws') )continue;
                                                    if ($role_id == $key && empty($this->data['Employee']['profile_account'])) {echo "<span style='margin-top:6px; position:absolute'> : &nbsp; " . __($value, true) . "</span>"; continue; }
                                                }
                                                ?>
                                                <input type="hidden" id="EmployeeRoleId" name="data[Employee][role_id]" value="<?php echo $role_id;  ?>" />
                                                <?php
                                            } else {
                                                if( $readOnly ): ?>
                                                <span style="display: inline-block; margin-top: 6px;">
                                                    <?php if( $this->data['Employee']['ws_account'] ): ?>
                                                        <?php __('Webservice') ?>
                                                    <?php else: ?>
                                                        <?php __($roles[2]) ?>
                                                    <?php endif ?>
                                                </span>
                                                <?php else: ?>
                                                <select name="data[Employee][role_id]" id="EmployeeRoleId" style="width:23%; color: #000" class="form-error" <?php if( $employee_info['Employee']['id'] == $id && $employeeLogin['Employee']['is_sas'] == 0)echo 'readonly' ?>>
                                                    <option value=""><?php echo __("---Select role---") ?></option>
                                                    <?php
                                                    foreach ($roles as $key => $value) {
                                                        //dont allow the pm to select company admin role
                                                        if( $is_pm && ($key == 2 || $key == 'ws') )continue;
                                                        if ( ($key == 'ws' && $this->data['Employee']['ws_account']) || (!$this->data['Employee']['ws_account'] && $role_id == $key) ){
                                                            echo "<option selected value='" . $key . "'>" . __($value, true) . "</option>";
                                                        } else if(!empty($this->data['Employee']['profile_account']) && 'profile_' . $this->data['Employee']['profile_account'] == $key) {
                                                            echo "<option selected value='" . $key . "'>" . __($value, true) . "</option>";
                                                        } else {
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
                                                <?php endif ?>
                                            <?php } ?>
                                            <input type="hidden" name="data[Employee][refer_id]" value="<?php echo $refer_id ?>" />
                                        </div>
                                        <!-- ticket -->
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
                                            // ob_clean();
                                            //     debug($profitCenters);
                                            //     debug($oldProfitCenter);
                                            //     exit;
                                            ?>
                                            <label><?php echo __('Profit Center') ?></label>
                                            <?php
                                            if (!empty($this->data['ProjectEmployeeProfitFunctionRefer']))
                                                $value = $this->data['ProjectEmployeeProfitFunctionRefer'][0]['profit_center_id']; else
                                                $value = "";
                                            echo $this->Form->input('profit_center_id', array('value' => $value,
                                                'type' => 'select',
                                                'div' => false,
                                                'label' => false,
                                                'style' => 'width: 61.5%',
                                                //  "empty" => __("-- Select -- ", true),
                                                "options" => $arr_new + $arr_tam));
                                            echo $this->Form->input('oldPc', array('type' => 'hidden', 'value' => $oldProfitCenter));
                                            ?>
                                        </div>
                                        <div class="wd-input">
                                            <label><?php echo __('Average Daily Rate') ?></label>
                                            <?php
                                            echo $this->Form->input('tjm', array(
                                                'type' => 'text',
                                                'div' => false,
                                               // 'style' => 'display:none',
                                                'label' => false));
                                            ?>
                                        </div>
                                        <div class="wd-input">
                                            <div style="float: left; width: 144px;" id="gLableExternal">
                                                <label for="budget"><?php __("external") ?></label>
                                                <label for="code_id" style="margin-top: 7px; display: none;" class="enterpriseCompany"><?php __("External company") ?></label>
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
                                                <label for="code_id" style="width: 14%;"><?php __("Actif") ?></label>
                                                <?php echo $this->Form->input('actif', array('options' => array(__('Not Actif', true), __('Actif', true)), 'div' => false, 'label' => false, 'style' => 'width: 23%')); ?>
                                            </div>
                                            <div>
                                            <label id="label-external" style="width: 36%;clear: both;color:red;display: none;"><?php __("Please select external company") ?></label>
                                            </div>
                                        </div>
                                        <div class="wd-input">
                                            <label for=""><?php __d(sprintf($_domain, 'Resource'), "ID3") ?></label>
                                            <?php echo $this->Form->input('id3', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                            <label for="budget" style="width: 14%;"><?php __d(sprintf($_domain, 'Resource'), "ID4") ?></label>
                                            <?php echo $this->Form->input('id4', array('div' => false, 'label' => false, 'style' => 'width: 21.8% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for=""><?php __d(sprintf($_domain, 'Resource'), "ID5") ?></label>
                                            <?php echo $this->Form->input('id5', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                            <label for="budget" style="width: 14%;"><?php __d(sprintf($_domain, 'Resource'), "ID6") ?></label>
                                            <?php echo $this->Form->input('id6', array('div' => false, 'label' => false, 'style' => 'width: 21.8% !important')); ?>
                                        </div>
                                        <div class="wd-input">
                                            <label><?php echo __('Capacity') ?>/<?php echo __('Year') ?></label>
                                            <?php
                                            echo $this->Form->input('capacity_by_year', array(
                                                'type' => 'text',
                                                'div' => false,
                                                //style' => 'display:none',
                                                'label' => false));
                                            ?>
                                        </div>
                                    <?php if($manage_hour): ?>
                                        <div class="wd-input">
                                            <label><?php echo __('Capacity in hour/day') ?></label>
                                            <div class="wd-capacity-hour">
                                                <!-- <label><span><?php echo __('Hour') ?></span><span><?php echo __('Minute') ?></span><span><?php echo __('Total minutes') ?></span></label> -->
                                                <div class="wd-input-capacity-hour">
                                                    <label style="width : 7%"><?php echo __('Hour:') ?></label>
                                                    <?php
                                                    echo $this->Form->input('capacity_in_hour', array(
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'style' => 'width: 10%;',
                                                        'value' => $capacity_hour,
                                                        'label' => false));
                                                    ?>
                                                </div>
                                                <div class="wd-input-capacity-hour">
                                                    <label style="width : 7%"><?php echo __('Minute:') ?></label>
                                                    <?php
                                                    echo $this->Form->input('capacity_in_minute', array(
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'value' => $capacity_minutes,
                                                        'style' => 'width: 10%;',
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
                                            <label for="budget" style="width: 13%;"><?php __("Post code"); ?></label>
                                            <?php echo $this->Form->input('post_code', array('div' => false, 'label' => false, 'style' => 'width: 22% !important')); ?>
                                        </div>

                                        <div class="wd-input">
                                            <label for="budget"><?php __("Country") ?></label>
                                            <?php echo $this->Form->input('country_id', array('div' => false, 'label' => false, 'style' => 'width: 23.5% !important')); ?>
                                        </div>
                                        <div class="div-table">
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('email_receive', array('div'=>false, 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __("Authorize z0 Gravity email") ?></b>
                                                </div>
                                            <?php if( $role_id < 4 ): ?>
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('auto_timesheet', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Auto validate timesheet') ?></b>
                                                </div>
                                            <?php endif ?>
                                            </div>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('activate_copy', array('div'=>false, 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __("Activate COPY in timesheet") ?></b>
                                                    <a href="javascript:void(0)" style="vertical-align: middle; margin-left: 10px" class="copy-timesheet" title="<?php __('Copy Forecast')?>"></a>
                                                </div>
                                            <?php if( $role_id < 4 ): ?>
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('auto_absence', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Auto validate absence') ?></b>
                                                </div>
                                            <?php endif ?>
                                            </div>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('is_enable_popup', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Enable popup') ?></b>
                                                </div>
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('auto_by_himself', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Auto validate by himself') ?></b>
                                                </div>
                                            </div>
                                            <?php /*  if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1){ ?>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('is_enable_new_design', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Enable checkbox new design') ?></b>
                                                </div>
                                            </div>
                                            <?php } */ ?>
                                        </div>
                                        <div class="wd-input wd-calendar">
                                            <label for="originaldate"><?php __("Work phone") ?></label>
                                            <?php
                                            echo $this->Form->input('work_phone', array('div' => false, 'label' => false));
                                            ?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="finality"><?php __("Mobile phone"); ?></label>
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
                                        <div class="wd-input">
                                            <label><?php echo __('Skill') ?></label>
                                        <?php if( $readOnly ): ?>
                                            <?php echo implode(', ', $def) ?>
                                        <?php else : ?>
                                            <?php
                                            echo $this->Form->input('project_function_id', array(
                                                'type' => 'select',
                                                'div' => false,
                                                'label' => false,
                                                "empty" => __("-- Select -- ", true),
                                                "options" => $projectFunctions));
                                            ?>
                                        <?php endif; ?>
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
                                            <label for="code_id"><?php __("Start date") ?></label>
                                            <?php echo $this->Form->input('start_date', array('type' => 'text', 'div' => false, 'label' => false)); ?>
                                            <span style="display: none; float:left; color: #000; width: 62%" id= "valueStartDate"></span>
                                        </div>
                                        <div class="wd-input">
                                            <label for="budget"><?php __("End date") ?></label>
                                            <?php echo $this->Form->input('end_date', array('type' => 'text', 'div' => false, 'label' => false)); ?>
                                            <span style="display: none; float:left; color: #000; width: 62%" id= "valueEndDate"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php if( !$readOnly ): ?>
                            </div>
                            <div id="multiResource" class="tab-panel">
                                    <ul class="tab-link">
                                        <li class="active"><a href="<?php echo "#tabs".$first; ?>"><?php echo $first; ?></a></li>
                                        <?php foreach ($dates as $key => $_date) {
                                            if($key != $first){?>
                                        <li><a href="<?php echo "#tabs".$key; ?>"><?php echo $key; ?></a></li>
                                        <?php } } ?>
                                    </ul>
                                <div class="content-area">
                                    <?php foreach ($dates as $key => $_date){?>
                                        <?php
                                        $_class = 'inactive';
                                        if($key == $first){
                                            $_class = 'active';
                                        }
                                        ?>
                                        <div id="<?php echo "tabs".$key; ?>" class="<?php echo $_class;?>">
                                        <h3 class="totalOfYear"><?php echo __('Total: ');?><span class="total-<?php echo $key;?>"><?php echo !empty($sumYearOfMutis[$key]) ? $sumYearOfMutis[$key] : 0;?></span></h3>
                                        <?php foreach ($_date as $k => $date) {
                                            $temp = $date;
                                            ?>
                                    <div id="absence-wrapper" style="margin-top: 30px;" class="wrap-<?php echo date('m-Y', array_shift($temp));?>">
                                        <table id="absence-fixed">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <span><?php switch ($k) {
                                                            case '01':
                                                                echo __('January', true);
                                                                break;
                                                            case '02':
                                                                echo __('February', true);
                                                                break;
                                                            case '03':
                                                                echo __('March', true);
                                                                break;
                                                            case '04':
                                                                echo __('April', true);
                                                                break;
                                                            case '05':
                                                                echo __('May', true);
                                                                break;
                                                            case '06':
                                                                echo __('June', true);
                                                                break;
                                                            case '07':
                                                                echo __('July', true);
                                                                break;
                                                            case '08':
                                                                echo __('August', true);
                                                                break;
                                                            case '09':
                                                                echo __('September', true);
                                                                break;
                                                            case '10':
                                                                echo __('October', true);
                                                                break;
                                                            case '11':
                                                                echo __('November', true);
                                                                break;
                                                            default:
                                                                echo __('December', true);
                                                                break;
                                                        } ?></span>
                                                    </th>
                                                    <th style="width:40px;"><?php echo __('Total', true);?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="absence-table-fixed"    >
                                                <tr>
                                                    <td class="ct" style="width: 70px; height: 22px;">
                                                        <?php $i = 0;
                                                        foreach ($date as $value){
                                                            if($i == 0){
                                                                $start = $value;
                                                            }
                                                            $i ++;
                                                        }?>
                                                        <input type="text" class="input_value_multi" id="<?php echo 'id'.$start; ?>" onchange="updateValueMultiResource(<?php echo $id; ?>, <?php echo $start; ?>, <?php echo $value; ?>, <?php echo $start; ?>, this.value, '<?php echo 'wrap-' . date('m-Y', $start);?>', '<?php echo 'cell-' . date('m-Y', $start);?>', '<?php echo date('m', $start);?>', '<?php echo date('Y', $start);?>')"
                                                            value="<?php
                                                                //set value for by date
                                                                if(!empty($employee_multi[strtotime('01-'.$k.'-'.$key)]['by_day'])){
                                                                    echo number_format($employee_multi[strtotime('01-'.$k.'-'.$key)]['by_day'], 2, '.', ' ');
                                                                }else if(!empty($employee_multi[strtotime('03-'.$k.'-'.$key)]['by_day'])){
                                                                    echo number_format($employee_multi[strtotime('03-'.$k.'-'.$key)]['by_day'], 2, '.', ' ');
                                                                }else if(!empty($employee_multi[strtotime('05-'.$k.'-'.$key)]['by_day'])){
                                                                    echo number_format($employee_multi[strtotime('05-'.$k.'-'.$key)]['by_day'], 2, '.', ' ');
                                                                }else{
                                                                    echo '0.00';
                                                                }
                                                            ?>">
                                                    </td>
                                                    <td style="text-align: right; width:50px;" class="total-<?php echo date('m-Y', $start);?> wrap-<?php echo date('Y', $start);?>"><?php echo !empty($sumMonthOfMutis[date('m-Y', $start)]) ? number_format($sumMonthOfMutis[date('m-Y', $start)], 2, '.', ' ') : '0.00';?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div id="absence-scroll">
                                            <table id='absence'>
                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($date as $value) {
                                                            echo "<th>";
                                                            echo date('d',$value);
                                                            echo ' ';
                                                            echo __(date('M', $value));
                                                            echo "</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody id="absence-table" class="ui-selectable">
                                                    <tr>
                                                        <?php
                                                        foreach ($date as $value) {
                                                            if( strtolower(date('l', $value)) == 'saturday' || strtolower(date('l', $value)) == 'sunday' ){
                                                                echo "<td class = 'ct' style='min-width: 70px;'>"."</td>";
                                                            }else if (!empty($days) && in_array($value, $days)){
                                                                echo "<td style='min-width: 70px; background-color: #ffff00; text-align: center;'>".__('Holiday', true)."</td>";
                                                            }else{
                                                        ?>
                                                            <td style ="min-width: 70px;">
                                                                <input type="text" rel="no-history" id="<?php echo $value; ?>" class="<?php echo $start; ?> <?php echo 'cell-' . date('m-Y', $start);?> input_value_multi" onchange="updateValue(<?php echo $id; ?>, <?php echo $value; ?>, this.value, '<?php echo 'wrap-' . date('m-Y', $start);?>', '<?php echo 'cell-' . date('m-Y', $start);?>', '<?php echo date('m', $start);?>', '<?php echo date('Y', $start);?>')"
                                                                value="<?php echo !empty($employee_multi[$value]['value']) ? number_format($employee_multi[$value]['value'], 2, '.', ' ') : '0.00'; ?>" />
                                                            </td>
                                                        <?php
                                                            }
                                                        }
                                                         ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php }?> </div> <?php }?>
                                </div>
                            </div>
                                <div class="wd-submit" >
                                    <button style="margin-top: 10px;" type="submit" class="btn-text btn-green" id="btnSave" />
                                        <img src="<?php echo $this->Html->url('/img/ui/blank-save.png') ?>" />
                                        <span><?php __('Save') ?></span>
                                    </button>
                                    <a href="" style="margin-top: 10px;" class="btn-text btn-red" id="reset">
                                        <img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
                                        <span><?php __('Reset'); ?></span>
                                    </a>
                                </div>
                            <?php endif ?>
                            </fieldset>
                            <?php echo $this->Form->end() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="dialog_skip_value" class="buttons" style="display: none;">
    <div id="dialog_skip_value_contents">
        <div id="show-value" >
        </div>
        <h2 id="title-absences"><?php echo __('Modify the end date even if the resource has absences ?', true); ?></h2>
        <div id="show-absence" >
        </div>
    </div>
    <div style="position: absolute ;bottom: 5px;right: 10px;">
        <button class="btn-default" id="btn-yeschange"><?php echo __("Yes", true) ?></button>
        <button id="btn-nochange" class="btn-default"><?php echo __("No", true) ?></button>
    </div>
</div>
<div id='modal_dialog_confirm' style="display: none">
    <div class='title'>
    </div>
    <input class="ok-new-project" type='button' value='<?php echo __('OK', true) ?>' id='btnYes' />
    <input class="cancel-new-project" type='button' value='<?php echo __('Cancel', true) ?>' id='btnNo' />
</div>
<?php echo $html->script('validateDate'); ?>
<script language="javascript">
    function number_format(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
    var arrPosition=$('#EmployeePassword').position();
    var widthIt = $('#EmployeePassword').width();
    var leftMargin = widthIt+arrPosition.left;
    var id = <?php echo $id; ?>;
    var codeStart = <?php echo json_encode(__('Start date is not validate', true));?>;
    var codeEnd = <?php echo json_encode(__('End date is not validate', true));?>;
    var codeErroStartDay = <?php echo json_encode(__('There is a workload before the start date', true));?>;
    var codeErroEndDay = <?php echo json_encode(__('There is a workload after the end date', true));?>;
    $('#ch_avatar').css({'left':leftMargin+'px'});
    $(window).resize(function(e) {
        var arrPosition=$('#EmployeePassword').position();
        var widthIt = $('#EmployeePassword').width();
        var leftMargin = widthIt+arrPosition.left;
        $('#ch_avatar').css({'left':leftMargin+'px'});
    });
    <?php if( $readOnly ): ?>
    $('#EmployeeEditForm input, #EmployeeEditForm select').prop('disabled', true);
    <?php endif ?>
    $("select[name='data[Employee][project_function_id]']").multiSelect({noneSelected: 'Select skills', oneOrMoreSelected: '*', selectAll: false });

    function reset_form(){
        removeMessage();
    }
    if($('#EmployeeExternal').val() == 2){
        $('#multiResource').show();
    }else {
        $('#multiResource').hide();
    }
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
    if($('#EmployeeExternal').val() == 1 && $('#EmployeeExternalId').val() == ''){
        $('#label-external').show();
        $('#btnSave').hide();
        $('#EmployeeExternalId').click(function(){
            if( $('#EmployeeExternalId').val() == ''){
                $('#label-external').show();
                $('#btnSave').hide();
            } else {
                $('#label-external').hide();
                $('#btnSave').show();
            }
        });
    }
    $('#EmployeeStartDate').on('change', function(){
        if($('#EmployeeExternal').val() == 2 && ( $('#EmployeeStartDate').val() == '00-00-0000' || $('#EmployeeStartDate').val() == '')){
            $('#valueStartDate').show();
            $('#valueStartDate').css('color', 'red');
            $('#valueStartDate').html(codeStart +'!');
        }else{
            $('#valueStartDate').hide();
        }
        if($('#EmployeeExternal').val() == 2){
            $.ajax({
                url:'<?php echo $html->url(array('action' => 'checkStartDate')); ?>',
                type: 'POST',
                data: {
                    id: id,
                    date: $('#EmployeeStartDate').val()
                },
                success: function(a) {
                    if(a == 'true'){
                        $('#valueStartDate').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                        $('#valueStartDate').show();
                        $('#valueStartDate').css('color', 'red');
                        $('#valueStartDate').html(codeErroStartDay +'!');
                        $('#btnSave').hide();
                    } else {
                        $("#valueStartDate").hide();
                        $('#btnSave').show();
                    }
                }
            });
        }
    });
    var createDialogThree = function(){
            $('#dialog_skip_value').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 550,
                open : function(e){
                    var $dialog = $(e.target);
                    $dialog.dialog({open: $.noop});
                }
            });
            createDialogTwo = $.noop;
        }
    var pre_endate;
    var next_enddate;
    $('#EmployeeEndDate').on('focus', function(){
        pre_endate = $('#EmployeeEndDate').val();
    });
    function check_end_date(id_employee, end_date){
        var result = 1;
        $.ajax({
            url:'<?php echo $html->url(array('action' => 'checkChangeEndDate')); ?>',
            type: 'POST',
            async: false,
            data: {
                id: id_employee,
                date: end_date
            },
            success: function(a) {
                if(a == 'false'){
                    result = 0;
                }
            }
        });
        return result;
    }
    $('#EmployeeEndDate').on('change', function(){
        next_enddate = $('#EmployeeEndDate').val();
        var allowModifyEndDate = check_end_date(id, $('#EmployeeEndDate').val());
        if(allowModifyEndDate == 1) {
            if($('#EmployeeExternal').val() == 2 && ( $('#EmployeeEndDate').val() == '00-00-0000' || $('#EmployeeEndDate').val() == '')){
                $('#valueEndDate').show();
                $('#valueEndDate').css('color', 'red');
                $('#valueEndDate').html(codeEnd +'!');
            } else {
                $('#valueEndDate').hide();
            }
            if($('#EmployeeExternal').val() == 2){
                $.ajax({
                    url:'<?php echo $html->url(array('action' => 'checkEndDate')); ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        date: $('#EmployeeEndDate').val()
                    },
                    success: function(a) {
                        if(a == 'true'){
                            $('#valueEndDate').html("<img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' />");
                            $('#valueEndDate').show();
                            $('#valueEndDate').css('color', 'red');
                            $('#valueEndDate').html(codeErroEndDay +'!');
                            $('#btnSave').hide();
                        } else {
                            $("#valueEndDate").hide();
                            $('#btnSave').show();
                        }
                    }
                });
            }
        } else {
            $("#show-value").html('');
            $("#show-absence").html('');
            $.ajax({
                url:'<?php echo $html->url(array('action' => 'getChangeEndDate')); ?>',
                type: 'POST',
                async: false,
                dataType: 'json',
                data: {
                    id: id,
                    date: $('#EmployeeEndDate').val()
                },
                success: function(data) {
                    if( !$.isEmptyObject(data.activities) ){
                        $.each(data.activities, function(index, value){
                            $("#show-value").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/activity_tasks/index/') ?>' + data.activities[index].ActivityTask.activity_id + '/?id='+ data.activities[index].ActivityTask.id +'" style="color:blue;font-size:13px">'+ data.activities[index].ActivityTask.name +', </a>, ');
                        });
                    }
                    if(!$.isEmptyObject(data.projects)) {
                        $.each(data.projects, function(index, value){
                            $("#show-value").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/project_tasks/index/') ?>'+ data.projects[index].ProjectTask.project_id +'/?id='+ data.projects[index].ProjectTask.id + '" style="color:blue;font-size:13px">'+data.projects[index].ProjectTask.task_title+', </a>');
                        });
                   }
                   if(!$.isEmptyObject(data.absences)){
                       $("#title-absences").show();
                       $.each(data.absences, function(index, value){
                           //data.absences[index].AbsenceRequest.date;
                            var date = new Date(parseInt(data.absences[index].AbsenceRequest.date) * 1000);
                            var month = date.getMonth() + 1;
                            var year = date.getFullYear();
                            if(data.absences[index].AbsenceAm.name){
                                $("#show-absence").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/absence_requests/index/month?id=') ?>'+ data.absences[index].Employee.id +'&profit='+ data.absences[index].Employee.profit_center_id + '&month='+ month + '&year=' + year + '&get_path=0' + '" style="color:blue;font-size:13px">' + data.absences[index].AbsenceAm.print + ', </a>');
                            }
                            if(data.absences[index].AbsencePm.name){
                                $("#show-absence").append('<a class="text-value" target="_blank" href="<?php echo $html->url('/absence_requests/index/month?id=') ?>'+ data.absences[index].Employee.id +'&profit='+ data.absences[index].Employee.profit_center_id + '&month='+ month + '&year=' + year + '&get_path=0' + '" style="color:blue;font-size:13px">' + data.absences[index].AbsencePm.print + ', </a>');
                            }
                       });
                   } else {
                       $("#title-absences").hide();
                   }
                    if(!$.isEmptyObject(data.activities) || !$.isEmptyObject(data.projects) || !$.isEmptyObject(data.absences)){
                        createDialogThree();
                        $("#dialog_skip_value").dialog('option',{title:'<h2 style="font-size:12px;"><?php echo h(__('Modify the end date even if the resource has task(s) assigned ?', true)); ?></h2>'}).dialog('open');
                        $('#EmployeeEndDate').val(pre_endate);
                    }
                }
            });
        }

    });
    $("#btn-yeschange").click(function(){
        $('#EmployeeEndDate').val(next_enddate);
        $('#dialog_skip_value').dialog('close');
    });
    $("#btn-nochange").click(function(){
        $('#EmployeeEndDate').val(pre_endate);
        $('#dialog_skip_value').dialog('close');
    });

    function updateValue(id, date, value, wrap, cell, month, year){
        var _id = '#'+date;
        $(_id).addClass('pch_loading loading_input');
        $(_id).css({
           'padding-left': '0px',
           'color': 'rgb(218, 215, 215)'
        });
        setTimeout(function(){
            $.ajax({
                url :'<?php echo $html->url(array('action' => 'update_value_multi_resource')); ?>',
                //async : false,
                type : 'POST',
                dataType : 'json',
                data: {
                    id: id,
                    date: date,
                    value: value
                },
                beforeSend: function(){

                },
                success: function(data){
                    var sumMonth = 0;
                    $('.' + wrap).find('.' + cell).each(function(){
                        var val = $(this).val();
                        val = val ? val : 0;
                        sumMonth += parseFloat(val);
                    });
                    $('.total-' + month + '-' + year).html(number_format(sumMonth, 2, '.', ' '));
                    var sumYear = 0;
                    $('#tabs'+year).find('.wrap-' + year).each(function(){
                        var val = $(this).html();
                        val = val ? parseFloat(val) : 0;
                        sumYear += val;
                    });
                    $('.total-' + year).html(number_format(sumYear, 2, '.', ' '));
                    setTimeout(function(){
                        $(_id).removeClass('pch_loading loading_input');;
                        $(_id).css('color', '#3BBD43');
                    }, 200);
                }
            });
        }, 200);
    }
    // viet lai
    function _updateValueMultiResource(id, start, end, idOf, by_day, wrap, cell, month, year){
        var _id = '#id'+idOf;
        var _class ='.'+idOf;
        $(_class).val($(_id).val());
        $(_id).addClass('pch_loading loading_input');
        $(_id).css('color', 'rgb(218, 215, 215)');
        setTimeout(function(){
            $.ajax({
                url :'<?php echo $html->url(array('action' => 'update_multi_resource')); ?>',
                type : 'POST',
                dataType : 'json',
                data: {
                    id: id,
                    start: start,
                    end: end,
                    by_day: by_day
                },
                beforeSend: function(){
                },
                success: function(data){
                    $('#btnSave').show();
                    var sumMonth = 0;
                    $('.' + wrap).find('.' + cell).each(function(){
                        var val = $(this).val();
                        val = val ? val : 0;
                        sumMonth += parseFloat(val);
                    });
                    $('.total-' + month + '-' + year).html(number_format(sumMonth, 2, '.', ' '));
                    var sumYear = 0;
                    $('#tabs'+year).find('.wrap-' + year).each(function(){
                        var val = $(this).html();
                        val = val ? parseFloat(val) : 0;
                        sumYear += val;
                    });
                    $('.total-' + year).html(number_format(sumYear, 2, '.', ' '));
                    setTimeout(function(){
                        $(_id).removeClass('pch_loading loading_input');
                        $(_class).removeClass('pch_loading loading_input');
                        $(_id).css('color', '#3BBD43');
                        $(_class).css('color', '#3BBD43');
                    }, 200);
                }
            });
        }, 500);
    };
    function updateValueMultiResource(id, start, end, idOf, by_day, wrap, cell, month, year){
        var _id = '#id'+idOf;
        var _class ='.'+idOf;
        $('.title').html('<?php echo __('Confirm ?') ?>');
        var dialog = $('#modal_dialog_confirm').dialog();
        $('#btnYes').click(function() {
            dialog.dialog('close');
            _updateValueMultiResource(id, start, end, idOf, by_day, wrap, cell, month, year);
        });
        $('#btnNo').click(function() {
            dialog.dialog('close');
            $('#btnSave').show();
        });
    };
    $(document).ready(function () {
        $('.tab-panel .tab-link a').on('click', function (e) {
            var currentAttrValue = jQuery(this).attr('href');
            $('.tab-panel ' + currentAttrValue).slideDown(400).siblings().slideUp(400);
            $(this).parent('li').addClass('active').siblings().removeClass('active');
            e.preventDefault();
        });
    });
    $('.input_value_multi').keypress(function(){
        $('#btnSave').hide();
    });
    //---end---
    $(function(){
        $("#EmployeeStartDate, #EmployeeEndDate").datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        });
    });
    var oldProfitCenter = <?php echo json_encode($oldProfitCenter); ?>;
        $('#btnSave').click(function(){
        removeMessage();
        var v1=false,v2=false,v3=true,v4=true,v5=true,v6=false;
        v1=isNotEmpty('EmployeeCompanyId');
        v2=isNotEmpty('EmployeeRoleId');
        if (isNotEmpty('EmployeePassword') || isNotEmpty('EmployeeConfirmPassword')) {
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
        var newPC = $('#EmployeeProfitCenterId').val();
        if(newPC == oldProfitCenter){
            v6 = true;
            //do nothing
        } else {
            if(v1&&v2&&v3&&v4&&v5){
                createDialog();
                $("#confirm_when_change_pc").dialog('open');
                $('#ok_attach').click(function(){
                    v6 = true;
                    $('#EmployeeEditForm').submit();
                });
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
    function isNotEmpty(elementId,cpr){
        var date = $("#"+elementId).val();
        if(date==''){
            if ((elementId != "EmployeeCompanyId") && (elementId != "EmployeeRoleId")&& (elementId != "EmployeeProfitCenterId")) return false;
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
        function removeMessage(){
            $("#flashMessage").hide();
            $('div.error-message').remove();
            $('div.error-message1').remove();
            $('div.error-message2').remove();
            $('div.error-message3').remove();
            $("div.wd-input,input,select").removeClass("form-error");
            check_phase_exist = false;
        };
        $('#EmployeeWorkPhone').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeeWorkPhone').val();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Work phone must be a number ", true)) ?>"+'</div>');
            } else{
                var fomrerror = $("#EmployeeWorkPhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeMobilePhone').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeeMobilePhone').val();
            $('div.error-message').remove();
            if(!rule.test(x)){
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.addClass("form-error");
                var parentElem = fomrerror.parent();
                parentElem.addClass("error");
                parentElem.append('<div class="error-message">'+"<?php echo h(__("The Mobile phone must be a number ", true)) ?>"+'</div>');
            } else{
                var fomrerror = $("#EmployeeMobilePhone");
                fomrerror.removeClass("form-error");
                $('div.error-message').remove();
            }
        });
        $('#EmployeeHomePhone').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeeHomePhone').val();
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
        $('#EmployeeFax').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeeFax').val();
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
        $('#EmployeePostCode').keypress(function(){
            var rule = /^([0-9]*)$/;
            var x=$('#EmployeePostCode').val();
            $('div.error-message').remove();
            $('div.error-message3').remove();
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
        var ar = <?php echo json_encode($abc) ?>;
        var tx = "";
        for (i = 0; i < ar.length; i++) {
            $("input[name='EmployeeProjectFunctionId[]'][value='" + ar[i] + "']").attr("checked","checked");
            $("input[name='EmployeeProjectFunctionId[]'][value='" + ar[i] + "']").parent().addClass("checked");
            tx += $("input[name='EmployeeProjectFunctionId[]'][value='" + ar[i] + "']").parent().text() + ",";
        }
        if (tx.length > 0) {
            tx = tx.substr(0, tx.length - 1);
            $("#EmployeeProjectFunctionId span").html(tx);
        }
        $("#EmployeeCodeId").change(function(){
            var code_id = $.trim($(this).val());
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
                        } else {
                            $('#btnSave').removeAttr('disabled');
                        }
                        $("#check").remove();
                    }
                })
            }
        })
        $('#edit_avatar_employee').click(function(){
            createDialogTwo();
            $("#avatar_popup").dialog('option',{title:'Avatar'}).dialog('open');
        });
        function getDoc(frame) {
            var doc = null;
            // IE8 cascading access check
            try {
                if (frame.contentWindow) {
                    doc = frame.contentWindow.document;
                }
            } catch(err) {}
            if (doc) { // successful getting content
                return doc;
            }
            try { // simply checking may throw in ie8 under ssl or mismatched protocol
                doc = frame.contentDocument ? frame.contentDocument : frame.document;
            } catch(err) {
                // last attempt
                doc = frame.document;
            }
            return doc;
        }
        /* $('#uploadForm').submit(function(e){
            if(window.FormData !== undefined){
                var formData = new FormData($(this)[0]);
                var formURL = $(this).attr("action");
                $.ajax({
                    url: formURL,
                    type: 'POST',
                    data:  formData,
                    mimeType:"multipart/form-data",
                    async: false,
                    cache: false,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        var nameAvatar = JSON.parse(data);
                        var link = <?php echo $this->UserFile->avatarjs('large') ?>.replace('{id}', id);
                        $('#ch_avatar').find('img').attr('src', link);
                    }
                });
                e.preventDefault(); //Prevent Default action.
            } else {
                var formObj = $(this);
                //generate a random id
                var iframeId = 'unique' + (new Date().getTime());
                //create an empty iframe
                var iframe = $('<iframe src="javascript:false;" name="'+iframeId+'" />');
                //hide it
                iframe.hide();
                //set form target to iframe
                formObj.attr('target',iframeId);
                //Add iframe to body
                iframe.appendTo('body');
                iframe.load(function(e){
                    var doc = getDoc(iframe[0]);
                    var docRoot = doc.body ? doc.body : doc.documentElement;
                    var data = docRoot.innerHTML;
                    //data is returned from server.
                });
            }
        }); */
        $("#avatar_popup_submit").live('click', function(){
            $("#uploadForm").submit(); //Submit the form
            $("#avatar_popup").dialog("close");
        });
        /* table .end */
        var createDialogTwo = function(){
            $('#avatar_popup').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 460,
                height      : 150,
                open : function(e){
                    var $dialog = $(e.target);
                    $dialog.dialog({open: $.noop});
                }
            });
            createDialogTwo = $.noop;
        }
        var createDialog = function(){
            $('#confirm_when_change_pc').dialog({
                position    :'center',
                autoOpen    : false,
                autoHeight  : true,
                modal       : true,
                width       : 400,
                open : function(e){
                    var $dialog = $(e.target);
                    $dialog.dialog({open: $.noop});
                }
            });
            createDialog = $.noop;
        }
        $(".cancel").live('click',function(){
            $("#confirm_when_change_pc").dialog('close');
            $("#avatar_popup").dialog("close");
        });
        $('#EmployeeActif').change(function(){
            var _sl = $(this).find('option:selected').val();
            if(_sl == 0){
                var currentDate = <?php echo json_encode(date('d-m-Y', time()));?>;
                var endDate = $('#EmployeeEndDate').val();
                if(endDate == ''){
                    $('#EmployeeEndDate').val(currentDate);
                }
            }
        });
        $('#ch_avatar, .ch-edit').hover(function(){
            $('.ch-edit').css('display', 'block');
        }, function(){
            $('.ch-edit').css('display', 'none');
        });
        $('#list-dialog').dialog({
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 400
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
        validWhenEmpty : true
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
    var control_resource = <?php echo $control_resource ?>;
    var create_a_project = <?php echo json_encode($this->data['Employee']['create_a_project']);?>;
    var change_status_project = <?php echo json_encode($this->data['Employee']['change_status_project']);?>;
    var delete_a_project = <?php echo json_encode($this->data['Employee']['delete_a_project']);?>;
    var can_communication = <?php echo json_encode($this->data['Employee']['can_communication']);?>;
    var can_see_forecast = <?php echo json_encode($this->data['Employee']['can_see_forecast']);?>;
    var can_control_resource = function(){
        if( $('#EmployeeRoleId').val() != 2 && $('#EmployeeRoleId').val() != 4 && $('#EmployeeRoleId').val() != 'ws' ){
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
            $('.change_status_project').show();
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
            $('.delete_a_project').show();
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
                    if((value['can_change_status_project'] && value['can_change_status_project'] == 1) || (change_status_project == 1 )){
                        $('#change_status_project').prop('checked', true);
                    } else {
                        $('#change_status_project').prop('checked', false);
                    }
                    if((value['can_create_project'] && value['can_create_project'] == 1) || (create_a_project == 1 )){
                        $('#create_a_project').prop('checked', true);
                    } else {
                        $('#create_a_project').prop('checked', false);
                    }
                    if((value['can_delete_project'] && value['can_delete_project'] == 1) || (delete_a_project == 1 )){
                        $('#delete_a_project').prop('checked', true);
                    } else {
                        $('#delete_a_project').prop('checked', false);
                    }
                    if((value['can_communication'] && value['can_communication'] == 1)){
                        $('#can_communication').prop('checked', true);
                    } else {
                        $('#can_communication').prop('checked', false);
                    }
                    if((value['can_see_forecast'] && value['can_see_forecast'] == 1)){
                        $('#can_see_forecast').prop('checked', true);
                    } else {
                        $('#can_see_forecast').prop('checked', false);
                    }
                    if((value['create_resource'] && value['create_resource'] == 1) || (control_resource == 1 )){
                        $('#control_resource').prop('checked', true);
                    } else {
                        $('#control_resource').prop('checked', false);
                    }
					//them dieu kien de uncheck khi edit user voi role Profile Project Manager.
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
    var update_budget = <?php echo json_encode($this->data['Employee']['update_budget']);?>;
    var update_your_form = <?php echo json_encode($this->data['Employee']['update_your_form']);?>;
    var see_all_projects = <?php echo $see_all_projects ?>;
    var see_budget = <?php echo $see_budget ?>;
    var sl_budget = <?php echo $sl_budget ?>;
    $('<div class="project-manage-colleft"><div class="control_resource"><input type="hidden" name="data[control_resource]" value="0" /><input type="checkbox" name="data[control_resource]" value="1" '+(control_resource==1 ? 'checked' : '')+' id="control_resource" /><?php __('Allow managing resources?') ?></div>').insertAfter('#EmployeeRoleId');
    $('<div class="see_all_projects"> <input type="hidden" name="data[see_all_projects]" value="0" /><input type="checkbox" name="data[see_all_projects]" value="1" '+(see_all_projects ? 'checked' : '')+' id="see_all_projects" /><?php __('See All Project') ?></div>').insertAfter('.control_resource');
    var slOp1 = slOp2 = slOp3 = '';
    if(sl_budget == 2){
        slOp3 = 'selected="selected"';
    }
    else if(sl_budget == 1){
        slOp2 = 'selected="selected"';
    } else {
        slOp1= 'selected="selected"';
    }
    var budgetHtml = '<div class="sl_budget"><select name="data[sl_budget]" class="sl_budget1"><option value="0" '+slOp1+'>' + <?php echo json_encode(__('Not Display Budget', true));?>+ '</option><option value="1" '+slOp2+'>' + <?php echo json_encode(__('Readonly Budget', true));?>+ '</option><option value="2" '+slOp3+'>' + <?php echo json_encode(__('Update Budget', true));?>+ '</option></select> </div>';
    $(budgetHtml).insertAfter('.see_all_projects');
    $('<div class="update_your_form"><input type="hidden" name="data[update_your_form]" value="0" /><input type="checkbox" name="data[update_your_form]" value="1" '+(update_your_form != 1 ? '' : 'checked')+' id="update_your_form" /><?php __('Update Your form') ?></div></div>').insertAfter('.sl_budget');

    $('<div class="project-manage-colright"><div class="create_a_project"><input type="hidden" name="data[create_a_project]" value="0" /><input type="checkbox" name="data[create_a_project]" value="1" '+(create_a_project != 1 ? '' : 'checked')+' id="create_a_project" /><?php __('Create a project') ?></div></div>').insertAfter('.project-manage-colleft');
    $('<br><div style="margin-left: 51.6%" class="delete_a_project"><input type="hidden" name="data[delete_a_project]" value="0" /><input type="checkbox" name="data[delete_a_project]" value="1" '+(delete_a_project != 1 ? '' : 'checked')+' id="delete_a_project" /><?php __('Delete a project') ?></div></div>').insertAfter('.create_a_project');
    $('<br><div class="change_status_project"><input type="hidden" name="data[change_status_project]" value="0" /><input type="checkbox" name="data[change_status_project]" value="1" '+(change_status_project != 1 ? '' : 'checked')+' id="change_status_project" /><?php __('Can change the status opportunity/in progress') ?></div></div>').insertAfter('.delete_a_project');
    $('<br><div class="can_communication"><input type="hidden" name="data[can_communication]" value="0" /><input type="checkbox" name="data[can_communication]" value="1" '+(can_communication != 1 ? '' : 'checked')+' id="can_communication" /><?php __('Can communicate communication') ?></div></div>').insertAfter('.change_status_project');
    $('<br><div class="can_see_forecast"><input type="hidden" name="data[can_see_forecast]" value="0" /><input type="checkbox" name="data[can_see_forecast]" value="1" '+(can_see_forecast != 1 ? '' : 'checked')+' id="can_see_forecast" /><?php __('Can see the forecast') ?></div></div>').insertAfter('.can_communication');

    $('#EmployeeRoleId').change(can_control_resource);
    can_control_resource();
    window.onload = function() {
        $('#EmployeePassword').val('');
    };
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
    // firt run calulator capacity in hour/day.
    var capacity_hour = <?php echo json_encode($capacity_hour) ?>,
        capacity_minutes = <?php echo json_encode($capacity_minutes) ?>,
        fill_more_than_capacity_day = <?php echo json_encode($fill_more_than_capacity_day) ?>,
        val = parseFloat(parseFloat(capacity_hour*60) + parseFloat(capacity_minutes));
    $('#EmployeeTotalMinutes').val(val);
    $('#EmployeeCapacityInHour, #EmployeeCapacityInMinute').keyup(function(){
        var val1 = $('#EmployeeCapacityInHour').val() ? $('#EmployeeCapacityInHour').val() : 0,
            val2 = $('#EmployeeCapacityInMinute').val() ? $('#EmployeeCapacityInMinute').val() : 0,
            val = parseFloat(parseFloat(val1*60) + parseFloat(val2));
        $('#EmployeeTotalMinutes').val(val);
    });
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
    $(document).ready(function() {
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
    $('#EmployeeTotalMinutes').prop( "disabled", true );
</script>
<!-- dialog_attachement_or_url -->
<div id="confirm_when_change_pc" class="buttons" style="display: none;">
    <p style="font-size: 13px; color: black; padding-left: 10px;"><?php echo __('You try to modify data being used. If you confirm, the information concerning employee will be changed.', true);?></p>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("No") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Yes') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->
<!-- avatar_popup -->
<div id="avatar_popup" style="display:none;" class="buttons">
    <?php
    echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'employees', 'action' => 'update_avatar', $company_id, $id)
    ));
    ?>
    <div class="wd-input" style="padding-left: 40px;">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/business/img-1.png"/><input type="file" id="textAvatar" name="FileField[attachment]" style="margin-left: 10px;font-size: 13px;"/></li>
        </ul>
        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
            <strong>Size:</strong>
            100px x 116px
        </p>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="avatar_popup_submit" class="new" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End avatar_popup -->
