<?php echo $html->script('jquery.validation.min');?>
<?php echo $html->script('strength');?>
<?php echo $this->Html->css('preview/my-profile'); ?>
<?php echo $this->element('dialog_projects') ?>

<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <div class="wd-title-left">
                        <img src="<?php echo $html->url('/img/new-icon/user.png') ?>" />
                        <h2><?php echo __("Mon profil Z0G", true); ?></h2>
                    </div>
                    <a href="<?php echo $html->url(array('controller' => 'profit_centers', 'action' => 'organization?link=' . $this->params['controller'] . "/" . $this->params['action'] . '|1')) ?>" class="btn-text">
                        <span><?php echo __('Organisation') ?></span>
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
                                <div class="wd-content">
                                    <div class="openMenu" onclick="openMenuLeft()";><img title="burger"  src="<?php echo $html->url('/img/new-icon/list-black.png'); ?>"/></div>
                                    <div class="wd-left-content">
                                        <div id="ch_avatar" class="wd-input">
                                            <?php
                                            $linkAvatar = $this->UserFile->avatar($employee_id, 'large');
                                            ?>
                                            <div class="avatar-inner">
                                             <img width="120" height="120" class="img-avatar" src="<?php echo $linkAvatar;?>" />
                                            </div>
                                            <div class="employee-name"><?php echo $fullname; ?></div>
                                            <div class="ch-edit">
                                                <a class="wd-edit" href="javascript:void(0);" id="edit_avatar_employee"><?php __("Changer d’image")?></a>
                                            </div>
                                        </div>
                                        <div class="wd-color-avat">
                                            <p><?php echo __('Choisissez une couleur pour votre avatar ou téléchargez une photo de profil') ?></p>
                                            <ul>
                                                <li><a href="#" style="background-color: #E4AF63;"></a></li>
                                                <li><a href="#" style="background-color: #6DAAD3;"></a></li>
                                                <li><a href="#" style="background-color: #67BD65;"></a></li>
                                                <li><a href="#" style="background-color: #7F78C3;"></a></li>
                                                <li><a href="#" style="background-color: #C6CCCF;"></a></li>
                                            </ul>
                                        </div>
                                        <div class="wd-color-theme">
                                            <p><?php echo __('Choisissez le thème de votre interface') ?></p>
                                            <ul>
                                                <li><a href="#"><img src="/img/new-icon/theme-1.jpg"/></a></li>
                                                <li><a href="#"><img src="/img/new-icon/theme-2.jpg"/></a></li>
                                                <li><a href="#"><img src="/img/new-icon/theme-3.jpg"/></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="wd-right-content">
                                        <div class="wd-right-content-inner">
                                        <div class="wd-my-info">
                                            <h2 class="wd-right-title"><?php echo __("Mes informations", true); ?></h2>
                                            <div class="wd-my-content wd-my-content-left">
                                                <div class="wd-my-content-left-inner">
                                                    <div class="wd-row">
                                                        <div class="wd-input">
                                                            <label for="project-name"><?php __("First Name")?></label>
                                                            <?php echo $this->Form->input('first_name', array('div'=>false, 'label'=>false, 'disabled' => true, 'style' => 'background-color: #F2F5F7'));?>
                                                        </div>
                                                        <div class="wd-input">
                                                            <label for="project-manager"><?php __("Last name")?></label>
                                                            <?php echo $this->Form->input('last_name', array('div'=>false, 'label'=>false, 'disabled' => true, 'style' => 'background-color: #F2F5F7'));?>
                                                        </div>
                                                    </div>
                                                    <div class="wd-input">
                                                        <label for="address"><?php __("Address")?></label>
                                                        <?php echo $this->Form->input('address', array('div'=>false, 'label'=>false, 'type'=>'text'));?>
                                                    </div>
                                                    <div class="wd-row">
                                                        <div class="wd-input wd-postal">
                                                            <label for="code_postal"><?php __("Code Postal")?></label>
                                                            <?php
                                                            echo $this->Form->input('code_postal', array('div'=>false, 'label'=>false, 'id'=>'code_postal', 'type'=>'text'));
                                                            ?>
                                                        </div>
                                                        <div class="wd-input wd-city">
                                                            <label for="city"><?php __("City")?></label>
                                                            <?php echo $this->Form->input('city_id', array('div'=>false, 'label'=>false, 'style'=>''));?>
                                                        </div>
                                                    </div>
                                                    <div class="wd-row">
                                                        <div class="wd-input wd-input-80">
                                                            
                                                            <label for="country" style=""><?php __("Country")?></label>
                                                            <?php echo $this->Form->input('country_id', array('div'=>false, 'label'=>false, 'style'=>''));?>
                                                        </div>
                                                        <div class="wd-input wd-calendar">
                                                            <label for="phone"><?php __("Work phone")?></label>
                                                            <?php
                                                            echo $this->Form->input('work_phone', array('div'=>false, 'label'=>false, 'id'=>'work_phone', 'type'=>'text'));
                                                            ?>
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div class="wd-my-content  wd-my-content-right">
                                                <div class="wd-my-content-right-inner">
                                                    <div class="wd-input">
                                                        <label for="current-phase"><?php __("Company: ")?></label>
                                                        <input type="text" disabled="disabled" style="background-color: #F2F5F7" maxlength="100" value="<?php echo $companyName;?>">
                                                    </div>
                                                    <div class="wd-row">
                                                        <div class="wd-input">
                                                        <label for="current-phase" style="width: 75px !important"><?php __("Role:")?></label>
                                                        <input type="text" disabled="disabled" style="background-color: #F2F5F7" maxlength="100" value="<?php echo !empty($profileName) ? $profileName : (($role_id == 'Project Manager') ? 'Project manager profil' : $role_id);?>">
                                                    </div>
                                                        <div class="wd-input">

                                                            <?php
                                                            $arr_new = array();
                                                            $arr_tam = array();
                                                            foreach ($profitCenters as $key => $value) {
                                                                $arr_tam[$key] = $value;
                                                            }
                                                            ?>
                                                            <label><?php echo __('Profit Center') ?></label>
                                                            <select disabled style="" name="data[Employee][profit_center_id]" style="" id="EmployeeProfitCenterId">
                                                            <?php
                                                                foreach ($arr_tam as $key => $value) {
                                                                    $select = ($key == $oldProfitCenter) ? "selected" : "";
                                                                    echo "<option value=" . $key . " ".$select.">".$value."</option>";
                                                                }
                                                            ?>
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="wd-connect">    
                                            <h2 class="wd-right-title"><?php echo __("Paramètres de connexion", true); ?></h2>
                                            <div class="wd-my-content wd-my-content-left">
                                                <div class="wd-my-content-left-inner">
                                                    <div class="wd-input">
                                                        <label for="priority"><?php __("Email")?></label>
                                                        <?php echo $this->Form->input('email', array('div'=>false, 'label'=>false, 'disabled' => true, 'style' => 'background-color: #F2F5F7'));?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wd-my-content wd-my-content-right">
                                                <div class="wd-my-content-right-inner">
                                                    <div class="wd-row">
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
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="clear: both"></div>
                                        </div>
                                        <div class="wd-socials"> 
                                            <div class="wd-my-content wd-my-content-left">
                                                <div class="wd-my-content-left-inner">
                                                    <h2 class="wd-right-title"><?php echo __("Réseaux sociaux", true); ?></h2>
                                                    <div class="wd-input">
                                                        <ul id="ch_group_infor_popup">
                                                            <li><img src="/img/new-icon/facebook.jpg"/><input type="text" id="textFacebook" /></li>
                                                            <li><img src="/img/new-icon/google-plus.jpg"/><input type="text" id="textGoogle" /></li>
                                                            <li><img src="/img/new-icon/twitter.jpg"/><input type="text" id="textTwitter" /></li>
                                                            <li><img src="/img/new-icon/linkedin.jpg"/><input type="text" id="textLinked" /></li>
                                                        </ul>
                                                        <p style="color: black;margin-left: 69px; font-size: 12px; font-style: italic;">
                                                            <strong>Ex:</strong>
                                                            www.example.com
                                                        </p>
                                                    </div>
                                                    <ul class="type_buttons" style="padding-right: 25px !important;">
                                                        <li><a style="display: none" class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
                                                        <li><a style="display: none" id="information_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
                                                        <li id="error"></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="wd-my-content wd-my-content-right">
                                                <div class="wd-my-content-right-inner">
                                                    <h2 class="wd-right-title"><?php echo __("Notifications", true); ?></h2>
                                                    <div class="div-table">
                                                        <div class="div-row">
                                                            <div class="div-cell">
                                                                <?php echo $this->Form->input('email_receive', array('div'=>false, 'label'=>false, 'style'=>'vertical-align:middle;float:none;'));?>
                                                                <span class="checkmark"></span>
                                                            </div>
                                                            <div class="div-cell div-cell-label">
                                                                <b><?php __("Authorize z0 Gravity email") ?></b><img src="/img/new-icon/question-dark.jpg"/>
                                                            </div>
                                                        </div>
                                                        <div class="div-row">
                                                            <div class="div-cell">
                                                                <?php echo $this->Form->input('activate_copy', array('div'=>false, 'label'=>false, 'style'=>'vertical-align:middle;float:none;'));?>
                                                                <span class="checkmark"></span>
                                                            </div>
                                                            <div class="div-cell div-cell-label">
                                                                <b><?php __("Activate COPY in timesheet") ?></b><img src="/img/new-icon/question-dark.jpg"/>
                                                                <a href="javascript:void(0)" style="" class="copy-timesheet" title="<?php __('Copy Forecast')?>"><img src="/img/new-icon/duplicate.jpg"/></a>

                                                            </div>
                                                        </div>
                                                        <div class="div-row">
                                                            <div class="div-cell">
                                                                <?php echo $this->Form->input('is_enable_popup', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'vertical-align:middle;float:none;'));?>
                                                                <span class="checkmark"></span>
                                                            </div>
                                                            <div class="div-cell div-cell-label">
                                                                <b><?php __('Enable popup') ?></b><img src="/img/new-icon/question-dark.jpg"/>
                                                            </div>
                                                        </div>
                                                        <?php if( $roleId < 4 ): ?>
                                                        <div class="div-row">
                                                            <div class="div-cell">
                                                                <?php echo $this->Form->input('auto_timesheet', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'vertical-align:middle;float:none;'));?>
                                                                <span class="checkmark"></span>
                                                            </div>
                                                            <div class="div-cell div-cell-label">
                                                                <b><?php __('Auto validate timesheet') ?></b><img src="/img/new-icon/question-dark.jpg"/>
                                                            </div>
                                                        </div>
                                                        <div class="div-row">
                                                            <div class="div-cell">
                                                                <?php echo $this->Form->input('auto_absence', array('div'=>false, 'type' => 'checkbox', 'label'=>false, 'style'=>'vertical-align:middle;float:none;'));?>
                                                                <span class="checkmark"></span>
                                                            </div>
                                                            <div class="div-cell div-cell-label">
                                                                <b><?php __('Auto validate absence') ?></b><img src="/img/new-icon/question-dark.jpg"/>
                                                            </div>
                                                        </div>
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="clear:both"></div>
                                        </div>
                                        <div class="wd-submit" style="clear: both;">
                                            <a href="" class="btn-submit btn-red" id="reset">
                                                <span><?php __('Reset'); ?></span>
                                            </a>
                                            <button type="submit" class="btn-submit btn-green" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                            <div style="clear: both"></div>
                                            <div class="wd-panel-policy" style="margin-top:15px;">
                                            <?php
                                                if(Configure::read('Config.language') === 'fre'):
                                                    echo __('In accordance with the "and Freedoms" of January 6, 1978 amended in 2004, you have the right to access and correct information about you that you can exercise by contacting your administrator.');
                                                else: echo __('Conformément à la loi « informatique et libertés » du 6 janvier 1978 modifiée en 2004, vous bénéficiez d’un droit d’accès et de rectification aux informations qui vous concernent, que vous pouvez exercer en vous adressant à votre administrateur.');
                                                endif;
                                            ?>
                                        </div>
                                        </div>

                                        
                                    </div>
                                    </div>
                                    
                                </div>
                                
                            </fieldset>
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
        <li><a style="display: none" class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a style="display: none" id="avatar_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End avatar_popup -->
<!-- group_information_popup -->
<div id="group_information_popup" style="display:none;" title="Information" class="buttons">
    <div class="wd-input">
        <ul id="ch_group_infor_popup">
            <li><img src="/img/new-icon/facebook.jpg"/><input type="text" id="textFacebook" /></li>
            <li><img src="/img/new-icon/twitter.jpg"/><input type="text" id="textGoogle" /></li>
            <li><img src="/img/new-icon/linkedin.png"/><input type="text" id="textTwitter" /></li>
            <li><img src="/img/new-icon/google-plus.png"/><input type="text" id="textViadeo" /></li>
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
    function openMenuLeft(){
        $('.openMenu').toggleClass('active');
        $('.openMenu').next('.wd-left-content').toggleClass('active');
        // header_bottom.find('.header-bottom-image').toggleClass('active');
    }
</script>
