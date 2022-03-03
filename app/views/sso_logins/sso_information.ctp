<?php 
echo $html->css(array(
	'layout_2019',
	'preview/layout',
	'preview/tab-admin'
));
echo $html->script(array(
	// 'responsive_table',
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
	.sso-img-button{
		width: 48px;
		height: 48px;
		margin-right: 1px;
		display: inline-block;
		text-align: center;
		background: #fff;
	}
	.sso-img-button:before{
		content: '';
		display: inline-block;
		width: 0;
		height: 100%;
		vertical-align: middle;
	}
	.sso-img-button .wd-icon{
		display: inline-block;
		vertical-align: middle;
	}
	.sso-img-button:hover img.wd-icon-hover{
		display: inline-block;
		vertical-align: middle;
	}
	.sso-img-button:hover img.wd-icon{
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
echo $this->Form->create('SsoInfo', array(
	'type' => 'POST',
	// 'url' => array(''),
	'class' => 'form-style-2019',
	// 'id' => 'SsoLogin'
));
?>
<div class="sso-config">
	<h4><?php __('SSO Config');?></h4>
	<?php 
	// debug( $this->data);
	$icons['embed'] = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" style="top:15px; position:relative;">
		  <rect id="Canvas" width="18" height="18" fill="#ff13dc" opacity="0"/>
		  <path class="svg-b" d="M8.226,14.473a.5.5,0,0,1-.483.371H7.227a.5.5,0,0,1-.472-.666L9.786,2.693a.5.5,0,0,1,.483-.37h.5a.5.5,0,0,1,.472.665Z" fill="#707070"/>
		  <path class="svg-b" d="M17.746,9.53l-4.095,4.16a.5.5,0,0,1-.713,0l-.446-.453a.5.5,0,0,1,0-.7L15.971,9,12.492,5.464a.5.5,0,0,1,0-.7l.446-.454a.5.5,0,0,1,.713,0l4.095,4.16A.76.76,0,0,1,17.746,9.53Z" fill="#707070"/>
		  <path  class="svg-b" d="M.254,8.47l4.1-4.161a.5.5,0,0,1,.713,0l.446.454a.5.5,0,0,1,0,.7L2.029,9l3.479,3.535a.5.5,0,0,1,0,.7l-.446.453a.5.5,0,0,1-.713,0L.254,9.53a.76.76,0,0,1,0-1.06Z" fill="#707070"/>
		</svg>';
	echo $this->Form->input('loginURL', array(
		'div' => 'wd-input', 
		'label' => __("Login URL", true),
		'rel' => 'no-history',
		'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span></span>',
		'disabled' => 'disabled',
	));
	echo $this->Form->input('SsoInfo.assertionConsumerService.url', array(
		'div' => 'wd-input', 
		'label' => __("ACS (Consumer) URL", true),
		'rel' => 'no-history',
		'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span></span>',
		'disabled' => 'disabled',
	));
	echo $this->Form->input('recipient_url', array(
		'div' => 'wd-input', 
		'label' => __("Recipient", true),
		'rel' => 'no-history',
		'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span></span>',
		'default' => $this->data['SsoInfo']['assertionConsumerService']['url'],
		'disabled' => 'disabled',
	));
	echo $this->Form->input('SsoInfo.singleLogoutService.url', array(
		'div' => 'wd-input', 
		'label' => __("Single Logout URL", true),
		'rel' => 'no-history',
		'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span></span>',
		'disabled' => 'disabled',
	));
	$entityId = !empty($this->data['SsoInfo']['entityId']) ?( $this->data['SsoInfo']['entityId'] . '?download=1') : '#';
	echo $this->Form->input('entityId', array(
		'div' => 'wd-input', 
		'label' => __("Audience", true). ' / ' . __("SP-entityId", true). ' / ' . __("Issuer", true),
		'rel' => 'no-history',
		'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span><a href="'. $entityId .'" class="sso-download download-xml sso-img-button" title="'.__('Click to download', true).'"><img class="wd-icon" alt="Download icon" src="/img/new-icon/download.png"><img class="wd-icon-hover wd-hidden" alt="Download icon" src="/img/new-icon/download-hover.png"></a></span>',
		'disabled' => 'disabled',
	));
	echo $this->Form->input('NameIDFormat', array(
		'div' => 'wd-input', 
		'label' => __("NameIDFormat", true),
		'rel' => 'no-history',
		'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span></span>',
		'disabled' => 'disabled',
	));
	?>
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
	}
	.monospaced{
		font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
	}
	.embed-copied{
		margin-left: 5px;
	}
	.embed-copied{
		line-height: 50px;
		color: #2362B8;
		margin-right: 6px;
	}
	.embed-copied:not(.wd-hidden),
	.copy-embed-code{
		display: inline-block;
		vertical-align: middle;
	}
	.copy-group{
		position: absolute;
		right: 0;
		top: 28px;
	}
	.wd-input .copy-embed-code{
		height: 50px;
		width: 50px;
		display: none;
		border: 1px solid #E1E6E8;
		background-color: #FFFFFF;
		border-radius: 3px;
		padding: 0;
		box-sizing: border-box;
		display: inline-block;
		text-align: center;
		transition: all 0.3s ease;
	}
	.wd-input .copy-embed-code:hover .svg-b{
		fill: #2362B8;
	}
</style>
<script>
$(window).ready(function(){
	if( Clipboard.isSupported()){
		var copied_timeout=0;
		$('.copy-embed-code').on('click', function(){
			var _this = $(this)
			$('.embed-copied').addClass('wd-hidden');
			var $temp = $("<input>");
			$("body").append($temp);
			var _cont = _this.closest('.wd-input');
			var _input = _cont.find('input');
			$temp.val(_input.val()).select();
			$temp[0].setSelectionRange(0, 99999);
			document.execCommand("copy"); /*For mobile devices*/
			$temp.remove();
			var _text = _cont.find('.embed-copied');
			_text.removeClass('wd-hidden');
			clearTimeout(copied_timeout);
			copied_timeout = setTimeout(function(){
				_text.addClass('wd-hidden');
			}, 3000)
		});
	}else{
		$('.copy-embed-code').hide();
	}
});
</script>