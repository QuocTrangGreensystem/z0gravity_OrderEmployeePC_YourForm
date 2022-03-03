<?php 
// debug( $employee_info); exit;
	// echo $html->script('jquery.validation.min');
	echo $html->script('strength');
	echo $this->Html->css('preview/my-profile'); 
	echo $this->element('dialog_projects');
	echo $html->css('dropzone.min'); 
	echo $html->css('preview/layout'); 
	echo $html->script('dropzone.min'); 
	$avatar_color = isset( $employee_info['Employee']['avatar_color'] ) ? $employee_info['Employee']['avatar_color'] : '#e5b063';
?>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <div class="wd-title-left">
                        <img src="<?php echo $html->url('/img/new-icon/user.png') ?>" />
                        <h2><?php echo __("My profile Z0G", true); ?></h2>
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
                                echo $this->Form->create('Employee', array(
                                    'type' => 'POST',
                                    'url' => array('controller' => 'employees_preview', 'action' => 'my_profile_preview', $employee_id)));
                                App::import("vendor", "str_utility");
                                $str_utility = new str_utility();
                            ?>
                            
                                <div class="wd-content">
									<div class="wd-content-container">
										<div class="wd-top-content">
											<div id="ch_avatar">
												<?php
												if( !empty( $employee_info['Employee']['avatar_resize']) ) {
													$linkAvatar = $this->Html->url(array('controller' => 'employees','action' => 'attachment', $employee_id, 'avatar', '?' => array('sid' => $api_key)), true);
													$text_avatar = '<img width="160" height="160" class="img-avatar" src="' . $linkAvatar . '"/>';
												}else{
													$avt_name = substr( trim($employee_info['Employee']['first_name']),  0, 1) .''.substr( trim($employee_info['Employee']['last_name']), 0, 1);
													$text_avatar = '<p class="circle-name" style="background-color: ' . (!empty( $employee_info['Employee']['avatar_color']) ? $employee_info['Employee']['avatar_color'] : '#72ADD2') .'">' . $avt_name . '</p>';
												}
											
												?>
												<div class="avatar-inner">
												<?php echo $text_avatar; ?>
												<div class="employee-name"><?php echo $employee_info['Employee']['fullname']; ?></div>
												<div class="ch-edit"><div class="ch-edit-inner">
													<a href="javascript:void(0);" onclick="openChEdit()" class="open-ch-edit-button">
														<span "true" class="icon-edit"></span>
													</a>
													<div class="ch-edit-open">
														<a class="wd-edit" href="javascript:void(0);" id="edit_avatar_employee"></a>
														<div class="wd-color-avat">
															<p><?php echo __('Choisissez une couleur pour votre avatar ou téléchargez une photo de profil') ?></p>
															<ul>
																<?php function is_avatar_color_checked($employee_info, $color){
																	if (!$color || empty($employee_info['Employee']['avatar_color'])) return '';
																	if( $employee_info['Employee']['avatar_color'] == $color ){
																		return "selected";
																	}
																	return '';
																} ?>
																<li class="<?php echo is_avatar_color_checked($employee_info, '#c7cdd0');?> "><a href="#" style="background-color: #c7cdd0;" data-color="#c7cdd0"></a></li>
																<li class="<?php echo is_avatar_color_checked($employee_info, '#8079c4');?> "><a href="#" style="background-color: #8079c4;" data-color="#8079c4"></a></li>
																<li class="<?php echo is_avatar_color_checked($employee_info, '#67be65');?> "><a href="#" style="background-color: #67be65;" data-color="#67be65"></a></li>
																<li class="<?php echo is_avatar_color_checked($employee_info, '#6dabd4');?> "><a href="#" style="background-color: #6dabd4;" data-color="#6dabd4"></a></li>
																<li class="<?php echo is_avatar_color_checked($employee_info, '#e5b063');?> "><a href="#" style="background-color: #e5b063;" data-color="#e5b063"></a></li>
															</ul>
															<?php 
															echo $this->Form->input('avatar_color', array(
																'type' => 'hidden',
															));
															?>
														</div>
													</div>
												</div></div>
											</div>
										</div>
										<div class="wd-bottom-content">
<div class="wd-bottom-content-inner">
	<div class="wd-my-info">
		<div class="wd-row">
			<div class=" wd-col wd-col-md-6 wd-my-content-left">
				<div class="wd-my-content-left-inner">
					<div class="wd-row">
						<div class="wd-col wd-col-md-6">
							<?php
							echo $this->Form->input('Role', array(
								'label' => __('Role', true),
								'type' => 'text',
								'disabled' => true,
								'value' => !empty($profileName) ? $profileName : (($role_id == 'Project Manager') ? 'Project manager profil' : $role_id),
								
							));
							?>
							
						</div>
						<div class="wd-col wd-col-md-6">
							<?php
							echo $this->Form->input('profit_center_id', array(
								'id' => 'EmployeeProfitCenterId',
								'type' => 'hidden',
								'label' =>  __('Profit Center', true),
								'value' => array_key_exists($employee_info['Employee']['profit_center_id'],$profitCenters) ? $employee_info['Employee']['profit_center_id']: '',
								
							));
							echo $this->Form->input('profit_center_name', array(
								'id' => 'EmployeeProfitCenterName',
								'type' => 'text',
								'disabled' => true,
								'label' =>  __('Profit Center', true),
								'value' => array_key_exists($employee_info['Employee']['profit_center_id'],$profitCenters) ? $profitCenters[$employee_info['Employee']['profit_center_id']]: '',
								
							));
							
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-md-12">
							<?php
							// debug( $this->data);exit;
							echo $this->Form->input('email', array(
								'type' => 'text',
								'label' => __("Email", true),
								'disabled' => true,
							));
							?>
						</div>
					</div>
					<div class="wd-row">
						<div class="wd-col wd-col-md-6">
							<?php 
								echo $this->Form->input('password', array(
									'label' => __("Password", true),
									'type' => 'password',
									'autocomplete'=> false,
									'value' => '',
									// 'before' => '<input type="password" style="display: none;" id="password-breaker" name="password-breaker" />'
								));								
							?>
						</div>
						<div class="wd-col wd-col-md-6">
							<?php 
								echo $this->Form->input('confirm_password', array(
									'label' => __("Confirm password", true),
									'type' => 'password',
									'autocomplete'=> false,
									'value' => '',
									'after' => '<div class="strength_meter veryweak pconfirm"></div>'
								));							
							?>
							
						</div>
					</div>
				
				   
				</div>
			</div>
			<div class=" wd-col wd-col-md-6 wd-my-content-right">
				<div class="wd-my-content-right-inner">
					<h2 class="wd-right-title"><?php echo __("Notifications", true); ?></h2>
				</div>
				<div class="div-row">
					<?php 
					echo $this->Form->input('email_receive', array(
						'label' => __("Authorize z0 Gravity email",true),
						'type' => 'checkbox',
						'between' => '<span class="checkmark"></span>',
					));
					?>
				</div>
				<div class="div-row">
					<?php 
					echo $this->Form->input('activate_copy', array(
						'label' => __("Activer la copie dans la feuille de temps",true),
						'between' => '<span class="checkmark"></span>',
						'type' => 'checkbox',
						'div' => array(
							'class' => 'input checkbox has-icon'
						)
					));
					?>
					<a href="javascript:void(0)" style="" class="copy-timesheet" title="<?php __('Copy Forecast')?>"><img src="/img/new-icon/duplicate.jpg"/></a>
				</div>
				<div class="div-row">
					<?php 
					echo $this->Form->input('is_enable_popup', array(
						'label' => __("Enable popup",true),
						'between' => '<span class="checkmark"></span>',
						'type' => 'checkbox'
					));
					?>
				</div>
				<div class="div-row">
					<?php 
					echo $this->Form->input('auto_timesheet', array(
						'label' => __("Auto validate timesheet",true),
						'between' => '<span class="checkmark"></span>',
						'type' => 'checkbox'
					));
					?>
				</div>
				<div class="div-row">
					<?php 
					echo $this->Form->input('auto_absence', array(
						'label' => __("Auto validate absence",true),
						'between' => '<span class="checkmark"></span>',
						'type' => 'checkbox'
						
					));
					?>
				</div>
			</div>
		</div>
		<div style="clear: both"></div>
	</div>
	
	<div class="wd-submit" style="clear: both;">
		<a href="" class="btn-submit btn-red" id="reset">
			<span><?php __('Reset'); ?></span>
		</a>
		<button type="submit" class="btn-submit btn-green" id="btnSave" />
			<span><?php __('Save') ?></span>
		</button>
		<div style="clear: both"></div>
		
	</div>
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
                                
                            
                            <?php echo $this->Form->end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- avatar_popup -->

<div id="avatar-upload" style="height: auto; width: 280px; display: none;">
    <div class="heading">
        <span class="close"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    </div>
    <div class="trigger-upload"><form id="upload-widget" onsubmit="completeAndRedirect()" method="post" action="/employees_preview/update_avatar/<?php echo $company_id ?>/<?php echo $employee_id ?>/false" class="dropzone" value="" >
        <input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
    </form></div>
</div>
<div class="light-popup"></div>

<div id="avatar_popup" style="display:none;" class="buttons">

    <form id="uploadForm" enctype="multipart/form-data" method="post" action="/employees_preview/update_avatar/<?php echo $company_id ?>/<?php echo $employee_id ?>/false" accept-charset="utf-8"
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
	$rules = $messages = array();
	if( $security['SecuritySetting']['complex_password'] ){
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
					var list = [], fn, ln;
					fn = <?php echo json_encode($employee_info['Employee']['first_name']); ?>,
					ln = <?php echo json_encode($employee_info['Employee']['last_name']); ?>;
					if( fn.length )list.push(fn);
					if( ln.length )list.push(ln);
					return list;
				},
			<?php endif ?>
			validWhenEmpty : true
		});
		/* password complexity and confirm password check */
		$('#EmployeeMyProfilePreviewForm').submit(function(){
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
			$('#EmployeeConfirmPassword').val('');
			
		});
		el.tooltip({
			maxHeight : 500,
			maxWidth : 400,
			type : ['top','left'],
			content: '<div class="password-rule"><h4><?php __('Password rules') ?></h4><ul><li>- ' + (<?php echo json_encode($rules) ?>).join('</li><li>- ') + '</li></ul></div>'
		});
	<?php }else{	?>
		$('#EmployeeMyProfilePreviewForm').submit(function(){
			if( $('#EmployeePassword').val() != '' && $('#EmployeePassword').val() !== $('#EmployeeConfirmPassword').val() ){
				$('.pconfirm').text('<?php __('Password confirm does not match') ?>');
				$('#EmployeeConfirmPassword').focus();
				return false;
			}
			$('#EmployeeConfirmPassword').val('');
		});
	<?php } ?> 
	$('#EmployeeConfirmPassword').on( 'keydown', function(){
		$('.pconfirm').text('');
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
    $('#avatar-upload .close').on( 'click', function (e) {
        // e.preventDefault();
        $("#avatar-upload").removeClass('show');
        $(".light-popup").removeClass('show');
    });
    $('#edit_avatar_employee').click(function(){
        $("#avatar-upload").addClass('show');
        $('.light-popup').addClass('show');
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
        console.log(1);
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
    $(".cancel").live('click',function(){
        $("#group_information_popup").dialog("close");
        $("#confirm_when_change_pc").dialog('close');
        $("#avatar_popup").dialog("close");
		location.reload();
    });
    function openMenuLeft(){
        $('.openMenu').toggleClass('active');
        $('.openMenu').next('.wd-left-content').toggleClass('active');
    }
    var myDropzone = new Dropzone("#upload-widget",{
        maxFiles: 1,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
    });
    myDropzone.on("success", function(file) {
        myDropzone.removeFile(file);
        location.reload();
    });
	function openChEdit(){
		$('.ch-edit').toggleClass('open');
	}
	$('.wd-color-avat ul li a').on('click', function(){
		$('#EmployeeAvatarColor').val($(this).data('color'));
		$('.wd-color-avat ul li').removeClass('selected');
		$(this).closest('li').addClass('selected');
		var avt_cl = $('#ch_avatar .avatar-inner .circle-name');
		if( avt_cl.length ) avt_cl.css('background-color', $(this).data('color'));
	});
</script>
