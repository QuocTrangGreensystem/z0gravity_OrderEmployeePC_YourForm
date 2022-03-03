<?php echo $html->script('jquery.validation.min');?>
<?php echo $html->script('strength');?>
<?php echo $this->element('dialog_projects') ?>
<style>
    #edit_avatar_employee{
        background-image: url('<?php echo $this->Html->webroot('img/front/camera.png'); ?>');
        padding: 9px;
        display: block;
        margin: 7px 39px;
    }
    #ch_avatar{
        margin-left: 145px;
    }
    #ch_avatar img.img-avatar{
        width: 100px;
        height: 116px;
    }
    .ch-edit{
        position: relative;
        background-color: rgb(245, 245, 245);
        width: 100px;
        height: 32px;
        opacity: 0.6;
        margin-top: -33px;
        padding-top: 1px;
        display: none;
    }
    #group-infor-address{
        clear: both;
        padding-left: 115px;
        margin-top: -30px;
    }
    #group-infor-address li {
        float: left;
    }
    a.wd-edit-group {
        background: url(<?php echo $this->Html->webroot('img/front/ico-edit.png') ?>) no-repeat left top;
        text-indent: -1983px;
        overflow: hidden;
        width: 23px;
        height: 24px;
        display: block;
        float: left;
        margin-top: 6.7px;
        margin-left: 6px;
    }
    #ch_group_infor_popup {
        padding-left: 25px;
    }
    #ch_group_infor_popup li {
        padding-bottom: 6px;
    }
    #ch_group_infor_popup li input {
        width: 360px;
        height: 22px;
        margin-top: 2px;
        margin-left: 10px;
        font-size: 13px;
    }
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
</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("My profile", true); ?></h2>
                    <a href="<?php echo $html->url(array('controller' => 'profit_centers', 'action' => 'organization?link=' . $this->params['controller'] . "/" . $this->params['action'] . '|1')) ?>" class="btn-text">
                        <img src="<?php echo $html->url('/img/ui/blank-organization.png') ?>" />
                        <span><?php echo __('Organization chart') ?></span>
                    </a>
                </div>
                <div class="wd-tab">
                    <ul class="wd-item">
                    </ul>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php
                                echo $this->Session->flash();
                                echo $this->Form->create('Employee', array('enctype'=>'multipart/form-data','autocomplete'=> "false"));
                                App::import("vendor", "str_utility");
                                $str_utility = new str_utility();
                            ?>
                            <fieldset>
                                <div>
                                    <div class="wd-left-content">
                                        <div class="wd-input">
                                            <label for="project-name"><?php __("First Name")?></label>
                                            <?php echo $this->Form->input('first_name', array('div'=>false, 'label'=>false, 'disabled' => true, 'style' => 'background-color: #eee'));?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="project-manager"><?php __("Last name")?></label>
                                            <?php echo $this->Form->input('last_name', array('div'=>false, 'label'=>false, 'disabled' => true, 'style' => 'background-color: #eee'));?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="current-phase"><?php __("Company: ")?></label>
                                            <div style="float:left; margin-top:6px;"><?php echo $companyName;?></div>
                                            <label for="current-phase" style="width: 75px !important"><?php __("Role:")?></label>
                                            <div style="float:left; margin-top:6px;"><?php echo !empty($profileName) ? $profileName : (($role_id == 'Project Manager') ? 'Project manager profil' : $role_id);?></div>
                                   
                                        </div>
                                        <div class="wd-input">

                                            <?php
                                            $arr_new = array();
                                            $arr_tam = array();
                                            foreach ($profitCenters as $key => $value) {
                                                // if (strtolower($value) == 'default') {
                                                //     $arr_new[$key] = $value;
                                                // } else {
                                                    $arr_tam[$key] = $value;
                                                // }
                                            }
                                            ?>
                                            <label><?php echo __('Profit Center') ?></label>
                                            <?php
                                            //    echo $this->Form->input('profit_center_id', array('value' => $value,
                                              //  'type' => 'select',
                                                // 'div' => false,
                                                // 'label' => false,
                                                // 'style' => 'width: 181px ; float: left',
                                                //  "empty" => __("-- Select -- ", true),
                                                // "options" => $arr_new + $arr_tam));
                                            //echo $this->Form->input('oldPc', array('type' => 'hidden', 'value' => $oldProfitCenter));
                                            ?>
                                            <select disabled style="width: 61.5%" name="data[Employee][profit_center_id]" style="" id="EmployeeProfitCenterId">
                                            <?php
                                                foreach ($arr_tam as $key => $value) {
                                                    $select = ($key == $oldProfitCenter) ? "selected" : "";
                                                    echo "<option value=" . $key . " ".$select.">".$value."</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="wd-input">
                                            <label for="priority"><?php __("Email")?></label>
                                            <?php echo $this->Form->input('email', array('div'=>false, 'label'=>false, 'disabled' => true, 'style' => 'background-color: #eee'));?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="status"><?php __("Password")?></label>
                                            <input type="password" style="display: none;" id="password-breaker" name="password-breaker" />
                                            <?php echo $this->Form->input('password', array('div'=>false, 'label'=>false, 'autocomplete'=> "false"));?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="status"><?php __("Confirm password")?></label>
                                            <?php echo $this->Form->password('confirm_password', array('div'=>false, 'label'=>false, "value"=>""));?>
                                            <div class="strength_meter veryweak pconfirm"></div>
                                        </div>
                                        <div class="wd-input wd-calendar">
                                            <label for="originaldate"><?php __("Work phone")?></label>
                                            <?php
                                            echo $this->Form->input('work_phone', array('div'=>false, 'label'=>false, 'id'=>'planed_end_date', 'type'=>'text'));
                                            ?>
                                        </div>
                                        <div class="wd-input wd-input-80">
                                            <label for="budget"><?php __("City")?></label>
                                            <?php echo $this->Form->input('city_id', array('div'=>false, 'label'=>false, 'style'=>'width: 25%'));?>
                                            <label for="budget" style="width: 15%;"><?php __("Country")?></label>
                                            <?php echo $this->Form->input('country_id', array('div'=>false, 'label'=>false, 'style'=>'width: 20% !important'));?>
                                        </div>
                                        <div class="wd-input">
                                            <label for="startdate"><?php __("Address")?></label>
                                            <?php echo $this->Form->input('address', array('div'=>false, 'label'=>false, 'type'=>'text'));?>
                                        </div>
                                    </div>
                                    <div class="wd-right-content">
                                        <div id="ch_avatar" class="wd-input">
                                            <?php
                                            $linkAvatar = $this->UserFile->avatar($employee_id, 'large');
                                            ?>
                                            <img class="img-avatar" src="<?php echo $linkAvatar;?>" />
                                            <div class="ch-edit">
                                                <a class="wd-edit" href="javascript:void(0);" id="edit_avatar_employee"></a>
                                            </div>
                                            <ul id="group-infor-address">
                                                <?php
                                                    $linkedFacebook = !empty($this->data['Employee']['facebook']) ? $this->data['Employee']['facebook'] : 'javascript:void(0);';
                                                    $linkedGoogle = !empty($this->data['Employee']['google']) ? $this->data['Employee']['google'] : 'javascript:void(0);';
                                                    $linkedTwitter = !empty($this->data['Employee']['twitter']) ? $this->data['Employee']['twitter'] : 'javascript:void(0);';
                                                    $linkedViadeo = !empty($this->data['Employee']['viadeo']) ? $this->data['Employee']['viadeo'] : 'javascript:void(0);';
                                                    $linkedLinkedin = !empty($this->data['Employee']['linkedin']) ? $this->data['Employee']['linkedin'] : 'javascript:void(0);';
                                                ?>
                                                <li><a href="http://<?php echo $linkedFacebook?>" target="_blank" id="linkFB"><img src="/img/business/face-1.png"/></a></li>
                                                <li><a href="http://<?php echo $linkedGoogle?>" target="_blank" id="linkGG"><img src="/img/business/google-1.png"/></a></li>
                                                <li><a href="http://<?php echo $linkedTwitter?>" target="_blank" id="linkTW"><img src="/img/business/twitter-1.png"/></a></li>
                                                <li><a href="http://<?php echo $linkedViadeo?>" target="_blank" id="linkVD"><img src="/img/business/viadeo-1.png"/></a></li>
                                                <li><a href="http://<?php echo $linkedLinkedin?>" target="_blank" id="linkLI"><img src="/img/business/linkedin-1.png"/></a></li>
                                            </ul>
                                            <div>
                                                <a class="wd-edit-group" href="javascript:void(0);" id="edit_group_infor">Edit</a>
                                            </div>
                                        </div>
                                        <div class="div-table">
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('email_receive', array('div'=>false, 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __("Authorize z0 Gravity email") ?></b>
                                                </div>
                                            </div>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('activate_copy', array('div'=>false, 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __("Activate COPY in timesheet") ?></b>
                                                    <a href="javascript:void(0)" style="vertical-align: middle; margin-left: 10px" class="copy-timesheet" title="<?php __('Copy Forecast')?>"></a>
                                                </div>
                                            </div>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('is_enable_popup', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Enable popup') ?></b>
                                                </div>
                                            </div>
                                            <?php if( $roleId < 4 ): ?>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('auto_timesheet', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Auto validate timesheet') ?></b>
                                                </div>
                                            </div>
                                            <div class="div-row">
                                                <div class="div-cell">
                                                    <?php echo $this->Form->input('auto_absence', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'width: auto !important;vertical-align:middle;float:none;'));?>
                                                </div>
                                                <div class="div-cell div-cell-label">
                                                    <b><?php __('Auto validate absence') ?></b>
                                                </div>
                                            </div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="wd-submit" style="clear: both;">
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
                            <div class="wd-panel-policy" style="margin-top:15px;">
                                <?php
                                    if(Configure::read('Config.language') === 'fre'):
                                        echo __('In accordance with the "and Freedoms" of January 6, 1978 amended in 2004, you have the right to access and correct information about you that you can exercise by contacting your administrator.');
                                    endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- avatar_popup -->
<div id="avatar_popup" style="display:none;" class="buttons">
    <?php
    echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'employees', 'action' => 'update_avatar', $company_id, $employee_id, 'false')
    ));
    ?>
    <div class="wd-input" style="padding-left: 40px;">
        <ul id="ch_group_infor_popup_1">
            <li><img src="/img/business/img-1.png"/><input type="file" id="textAvatar" name="FileField[attachment]" style="margin-left: 10px;font-size: 13px;"/></li>
        </ul>
        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
            <strong>Size:</strong>
            100px x 116px
        </p>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="avatar_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End avatar_popup -->
<!-- group_information_popup -->
<div id="group_information_popup" style="display:none;" title="Information" class="buttons">
    <div class="wd-input">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/business/face-1.png"/><input type="text" id="textFacebook" /></li>
            <li><img src="/img/business/google-1.png"/><input type="text" id="textGoogle" /></li>
            <li><img src="/img/business/twitter-1.png"/><input type="text" id="textTwitter" /></li>
            <li><img src="/img/business/viadeo-1.png"/><input type="text" id="textViadeo" /></li>
            <li><img src="/img/business/linkedin-1.png"/><input type="text" id="textLinked" /></li>
        </ul>
        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
            <strong>Ex:</strong>
            www.example.com
        </p>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="information_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
</div>
<!-- End group_information_popup -->
<script>
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
    /* password complexity and confirm password check */
    $('#EmployeeMyProfileForm').submit(function(){
        var valid = el.data('plugin_strength').valid;
        if( !valid ){
            $('#EmployeePassword').focus();
            return false;
        }
        if( $('#EmployeePassword').val() != '' && $('#EmployeePassword').val() !== $('#EmployeeConfirmPassword').val() ){
            $('.pconfirm').text('<?php __('Password confirm does not match') ?>');
            $('#EmployeeConfirmPassword').focus();
            return false;
        }
    });

    el.tooltip({
        maxHeight : 500,
        maxWidth : 400,
        type : ['top','left'],
        content: '<div class="password-rule"><h4><?php __('Password rules') ?></h4><ul><li>- ' + (<?php echo json_encode($rules) ?>).join('</li><li>- ') + '</li></ul></div>'
    });
<?php
endif;
?>
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
    $('.img-avatar, .ch-edit').hover(function(){
        $('.ch-edit').css('display', 'block');
    }, function(){
        $('.ch-edit').css('display', 'none');
    });
    $('#edit_avatar_employee').click(function(){
        createDialogTwo();
        $("#avatar_popup").dialog('option',{title:'Avatar'}).dialog('open');
    });
    $('#uploadForm').submit(function(e){
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
                    window.location = ('/employees/my_profile/');
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
                window.location = ('/employees/my_profile/');
            });
        }
    });
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
    $('#group_information_popup').dialog({
        position    :'center',
        autoOpen    : false,
        autoHeight  : true,
        modal       : true,
        width       : 460,
        height      : 290
    });
    if(typeof String.prototype.trim !== 'function') {
      String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
      }
    }
    $('#edit_group_infor').click(function(){
        var FB = ($('#linkFB').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkFB').attr('href').replace('http://', '').trim();
        var GT = ($('#linkGG').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkGG').attr('href').replace('http://', '').trim();
        var TW = ($('#linkTW').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkTW').attr('href').replace('http://', '').trim();
        var VD = ($('#linkTW').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkTW').attr('href').replace('http://', '').trim();
        var LI = ($('#linkLI').attr('href').replace('http://', '').trim() == 'javascript:void(0);') ? '' : $('#linkLI').attr('href').replace('http://', '').trim();
        $('#textFacebook').val(FB);
        $('#textGoogle').val(GT);
        $('#textTwitter').val(TW);
        $('#textViadeo').val(VD);
        $('#textLinked').val(LI);
        $('#group_information_popup').dialog("open");
    });
    $('#information_popup_submit').click(function(){
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'update_group_infor')); ?>',
            async: false,
            type : 'POST',
            dataType : 'json',
            data: {
                facebook: $('#textFacebook').val(),
                google: $('#textGoogle').val(),
                twitter: $('#textTwitter').val(),
                viadeo: $('#textViadeo').val(),
                linkedin: $('#textLinked').val(),
                company_id: <?php echo json_encode($employee_info['Employee']['company_id']);?>,
                id: <?php echo json_encode($employee_info['Employee']['id']);?>
            },
            success:function(data) {
                $('#linkFB').attr('href', 'http://' + data.facebook);
                $('#linkGG').attr('href', 'http://' + data.google);
                $('#linkTW').attr('href', 'http://' + data.twitter);
                $('#linkVD').attr('href', 'http://' + data.viadeo);
                $('#linkLI').attr('href', 'http://' + data.linkedin);
                $("#group_information_popup").dialog("close");
            }
        });
    });
    $(".cancel").live('click',function(){
        $("#group_information_popup").dialog("close");
        $("#confirm_when_change_pc").dialog('close');
        $("#avatar_popup").dialog("close");
    });
    //window.onload = function() {
      $('#EmployeePassword').removeAttr('value');
   //};
</script>
