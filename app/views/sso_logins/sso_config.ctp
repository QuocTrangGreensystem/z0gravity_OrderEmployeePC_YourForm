<?php 
echo $html->css(array(
	'layout_2019',
	'preview/layout',
	'preview/tab-admin',
    'dropzone.min',
    'layout_2019',
	'preview/component'
));
echo $html->script(array(
    'dropzone.min',
));
 ?>
<style>
	body {
		font-family: "Open Sans";
	}
	.wd-layout .wd-main-content .wd-tab {
		margin-left: auto;
		margin-right: auto;
		max-width: 1920px;
	}
	.wd-tab .wd-panel {
		background-color: #fff;
		border: none !important;
	}
	.upload-container-fixed #template_upload{
		display: block;
	}
	.upload-container-fixed{
		position: fixed;
		width: 100vw;
		height: 100vh;
		top: 0;
		left: 0;
		background: rgb(51,51,51,0.5);
		display: none;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
								<div id="formMessage" style="display: none;" class="message success"><?php __('Saved');?><a href="#" class="close">x</a></div>
                                <div class="wd-table" id="user-default-table" style="width:100%; overflow: auto">
<?php
echo $this->Form->create('SsoLogin', array(
	'type' => 'POST',
	// 'url' => array(''),
	'class' => 'form-style-2019',
	// 'id' => 'SsoLogin'
));
?>
<div class="sso-config">
	<h4><?php __('SSO Config');?></h4>
	<div class="wd-row">
		<div class="wd-col wd-col-lg-6">
		<?php 
		echo $this->Form->input('issuer_url', array('div' => 'wd-input','rel' => 'no-history', 'label' => __("Issuer URL (entityId)", true)));
		echo $this->Form->input('saml_end_point', array('div' => 'wd-input','rel' => 'no-history', 'label' => __("SAML2/HTTP-POST/sso", true)));
		echo $this->Form->input('slo_end_point', array('div' => 'wd-input','rel' => 'no-history', 'label' => __("SAML2/HTTP-redirect/slo", true)));
		
		?>
		</div>
		<div class="wd-col wd-col-lg-6">
		<?php 
		echo $this->Form->input('certificate', array(
			'class' => 'monospaced',
			'div' => 'wd-input', 
			'rel' => 'no-history',
			'type' => 'textarea', 
			'label' => __("X.509 Certificate", true)));
		?>
		</div>
	</div>
	<div id="test-connection"></div> 
</div>
<div class="wd-submit">
	<button type="reset" class="wd-button-f wd-reset-setting" id="btnReset">
		<span><?php __('Reset');?></span>
	</button>
	<!-- 
	<button type="button" class="wd-button-f wd-upload-setting" id="btnUpload">
		<span><?php __('Upload file');?></span>
	</button>
	-->
	<button type="submit" class="wd-button-f btn-right wd-save-project" id="btnSave">
		<span><?php __('Save');?></span>
	</button>
	<button type="button" class="wd-button-f wd-test-setting" id="btnTest">
		<span><?php __('Test');?></span>
	</button>
</div>
	<?php 
	echo $this->Form->end();
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
<div class="upload-container-fixed">
	<div id="template_upload" class="template_upload" style="height: auto; width: 350px;">
		<div class="heading">
			<h4><?php echo __('File upload(s)', true)?></h4>
			<span class="close close-popup"><img title="close"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
		</div>
		<?php 
		echo $this->Form->create('Upload', array(
			'type' => 'POST',
			'url' => array('controller' => $this->params['controller'], 'action' => 'upload' )
		));?>
			<div id="content_comment">
				<div class="append-comment"></div>
			</div> 
			<div class="wd-popup">
				
					<div class="trigger-upload"><div id="upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'upload' ));?>" class="dropzone" value="" >

					</div></div>
					<?php echo $this->Form->input('url', array(
						'class' => 'not_save_history',
						'rel' => 'no-history',
						'label' => array(
							'class' => 'label-has-sub',
							'text' =>__('URL Link',true),
							'data-text' => __('(optionnel)', true),
							),
						'type' => 'text',
						'id' => 'uploadURL',  
						'placeholder' => __('https://', true)));    
					?>
			</div>
			<div class="wd-submit actions" style="">
				<button type="reset" class="wd-button-f wd-reset-setting upload-cancel" id="btnResetUpload"><span><?php __("Upload Cancel") ?></span></button>
				<button type="submit" class="wd-button-f btn-right wd-save-project" id="btnSaveUpload"><span><?php __('Upload');?></span>	</button>
			</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<style>
	.form-style-2019 >div{
		margin-bottom: 40px;
	}
	.form-style-2019 div h4{
		margin-bottom: 10px;
	}
	.form-style-2019{
		padding: 0 20px;
	}
	.form-style-2019 .wd-input.wd-custom-checkbox label{
		font-weight: 400;
	}
	.form-style-2019 input[type="checkbox"] {
		display: inline-block;
		height: auto;
		width: auto;
	}
	.wd-tab .wd-aside-left {
		width: 245px !important;
	}
	#loadingElm{
		width: 15px;
		height: 15px;
		margin-left: 10px;
	}
	.wd-table.saving:before{
		content: '<?php __('Saving');?>';
		position: absolute;
		top: 0;
		right: 0;
		height: 20px;
		line-height: 20px;
		padding: 0 30px 0 20px;
		
	}
	.wd-content,
	.wd-table{
		position: relative;
	}
	.wd-content #formMessage{
		position: absolute;
		top: 0;
		right: 40px;
		max-width: 200px;
		z-index: 99;
	}
	#formMessage.success{
		background-color: #d5ffce;
		background-position: 15px -1505px;
		border-color: #82dc68;
	}
	#formMessage{
		background: #dbe3ff url(../img/common/message.gif) no-repeat 15px -6px;
	}
	.wd-tab .wd-panel{
		border: #d8d8d8 solid 1px;		
	}
	#accordion h3.head-title a{
		font-weight: 600 !important;
	}
	textarea{
		width: 100%;
		resize: vertical;
		border: 1px solid #E0E6E8;
		padding: 15px;
		height: 246px;
	}
	.monospaced{
		font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
	}
	#test-connection a{
		visibility: hidden;
	}
	#test-connection iframe{
		width: 100%;
		display: block;
		min-height: 220px;
		font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
		font-size: 12px;
		border: 1px solid #f2f5f7;
		background: #fafafa;
	}
	.wd-upload-setting:before {
		content: "\e084";
		font-family: 'simple-line-icons';
		font-size: 18px;
		vertical-align: middle;
		margin-right: 5px;
		color: #fff;
	}
	#template_upload .wd-submit .wd-button-f span{
		color: #fff;
	} 
</style>
<script>
var company_id = <?php echo json_encode($employee_info['Company']['id']);?>;
var test_uri = <?php echo json_encode($this->Html->url(array(
		'action' => 'trace',
		'?' => array(
			'data[login]' => 'true',
			'data[IssuerUrl]' => '%IssuerUrl%',
			'data[SamlEndPoint]' => '%SamlEndPoint%',
			'data[SloEndPoint]' => '%SloEndPoint%',
			'data[Certificate]' => '%Certificate%',
		)
	)));?>;
var new_uri;
function enable_check_button(){
	var IssuerUrl = $('#SsoLoginIssuerUrl').val(),
		SamlEndPoint = $('#SsoLoginSamlEndPoint').val(),
		SloEndPoint = $('#SsoLoginSloEndPoint').val(),
		Certificate = $('#SsoLoginCertificate').val();
	if( IssuerUrl && SamlEndPoint && SloEndPoint && Certificate){
		$('#btnTest').prop('disabled', false);
	}else{
		$('#btnTest').prop('disabled', true);
	}
}
enable_check_button();
$(window).ready(function(){
	$('#SsoLoginSsoConfigForm').on('change', function(){
		enable_check_button();
	});
	
	$('#btnTest').on('click', function(){
		if( $(this).is(':disabled')) return;
		var new_uri = test_uri;
		var IssuerUrl = $('#SsoLoginIssuerUrl').val(),
			SamlEndPoint = $('#SsoLoginSamlEndPoint').val(),
			SloEndPoint = $('#SsoLoginSloEndPoint').val(),
			Certificate = $('#SsoLoginCertificate').val();
		if( IssuerUrl && SamlEndPoint && SloEndPoint && Certificate){
			var idp_data = {
				company_id : company_id,
				login : 1,
				IssuerUrl : IssuerUrl,
				SamlEndPoint : SamlEndPoint,
				SloEndPoint : SloEndPoint,
				Certificate : Certificate
			};
			var _form = $('<form id="invisible_form" action="/sso_logins/trace" method="post" target="_blank"></form>');
			$.each( idp_data, function(k,v){
				_form.append('<input type="hidden" name="data[' + k + ']" value="' + v + '"/>');
			});
			$('#test-connection').empty().append(_form);
			_form.submit();
		}
	});
});

$('#btnUpload').on('click', function(){ $('.upload-container-fixed').show(); });
$('.upload-cancel, .close-popup').on('click', function(){  $('.upload-container-fixed').hide() });
Dropzone.autoDiscover = false;
var popupDropzone;
$(function() {
	popupDropzone = new Dropzone("#upload-popup",{
		maxFiles: 1,
		autoProcessQueue: true,
		addRemoveLinks: true,
		acceptedFiles: '.xml',
		
	});
	popupDropzone.on("success", function(file) {
		popupDropzone.removeFile(file);
	});
	popupDropzone.on("queuecomplete", function(file) {
		// location.reload();
	});
	$('#UploadSsoConfigForm').on('submit', function(e){
		$('#UploadSsoConfigForm').parent('.wd-popup').addClass('loading');
		// $('#popupUploadName').val($('#newDocName').val());
		// $('#popupUploadUrl').val($('#newDocURL').val());;

		e.preventDefault();
		if(popupDropzone.files.length){
			popupDropzone.processQueue();
		}else{
			$.ajax({
				type: 'POST',
				url: $('#UploadSsoConfigForm').prop('action'),
				data: {data: { Upload: {
					url: $('#uploadURL').val(),
				}}},
				success: function(res){
					console.log(res);
				},
			});
		}
		return false;
	});
	popupDropzone.on('sending', function(file, xhr, formData) {
		// Append all form inputs to the formData Dropzone will POST
		var data = $('#UploadSsoConfigForm').serializeArray();
		$.each(data, function(key, el) {
			formData.append(el.name, el.value);
		});
	});
});
        
    /* End Dropzone with form  */ 
</script>