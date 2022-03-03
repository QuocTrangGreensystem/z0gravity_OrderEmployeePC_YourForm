<?php
	$query = $this->params['url']['url'] . Router::queryString(array_diff_key(
                        $this->params['url'], array('url' => '', 'ext' => '')), array());
	$svg_icons = array(
		'logo-zog' => '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="76" viewBox="0 0 96 76"><defs><style>.a{fill:none;}.l,.d{fill:#1c557d;}.c{fill:#217fc2;}.d{fill-rule:evenodd;}</style></defs><g transform="translate(24)"><g transform="translate(0 0)"><circle class="a" cx="24" cy="24" r="24" transform="translate(0 0)"/><path class="l" d="M18.128,26.4a24.173,24.173,0,0,1-4.837-.488,23.876,23.876,0,0,1-4.505-1.4A23.994,23.994,0,0,1,4.71,22.3,24.17,24.17,0,0,1,1.158,19.37c-.4-.4-.787-.816-1.158-1.242l3.411-3.411A19.166,19.166,0,0,0,18.128,21.6,19.21,19.21,0,0,0,37.179,0h4.83c.079.795.119,1.6.119,2.4a24.171,24.171,0,0,1-.488,4.837,23.88,23.88,0,0,1-1.4,4.505,24.007,24.007,0,0,1-2.213,4.077A24.129,24.129,0,0,1,31.547,22.3a24,24,0,0,1-4.077,2.213,23.879,23.879,0,0,1-4.505,1.4A24.173,24.173,0,0,1,18.128,26.4Z" transform="translate(5.872 21.6)"/></g><g transform="translate(0 0)"><circle class="a" cx="24" cy="24" r="24" transform="translate(0 0)"/><path class="c" d="M18.395,37.269A14.4,14.4,0,0,1,9.8,26.4H.119C.04,25.605,0,24.8,0,24a24.189,24.189,0,0,1,.487-4.837A23.872,23.872,0,0,1,4.1,10.581a24.076,24.076,0,0,1,10.56-8.7,23.891,23.891,0,0,1,4.505-1.4,24.238,24.238,0,0,1,9.673,0A23.862,23.862,0,0,1,37.418,4.1a24.152,24.152,0,0,1,2.31,1.773L36.317,9.283A19.191,19.191,0,0,0,4.949,21.6H9.8a14.328,14.328,0,0,1,2.261-5.651,14.445,14.445,0,0,1,6.336-5.217,14.379,14.379,0,0,1,13.656,1.328c.292.2.582.409.861.629l-3.433,3.433A9.6,9.6,0,1,0,33.3,26.4H24V21.6H38.2a14.555,14.555,0,0,1,.2,2.4,14.331,14.331,0,0,1-2.459,8.051,14.448,14.448,0,0,1-6.336,5.217,14.45,14.45,0,0,1-11.21,0Z" transform="translate(0 0)"/></g></g><g transform="translate(0 56)"><path class="d" d="M90.255,20.01H87.418L89.7,14.733,85.478,4.408h2.988l2.723,7.4,2.76-7.4h2.833Zm-10.529-8.3v-5.1H78.47v-2.2h1.256V1.789H82.41V4.408h2.361v2.2H82.41v5.124c0,.707.288,1.013,1.123,1.013h1.238V15H83.1C81.078,15,79.726,14.14,79.726,11.712ZM75.367,3.148A1.57,1.57,0,0,1,75.2.012,1.731,1.731,0,0,1,77,1.529c0,.017,0,.034,0,.051a1.578,1.578,0,0,1-1.588,1.567h-.049ZM65.373,15,61.489,4.408h2.856l2.665,8.128,2.666-8.128h2.836L68.607,15Zm-8.052-1.548a4.153,4.153,0,0,1-3.5,1.721c-2.665,0-4.8-2.2-4.8-5.507s2.133-5.432,4.817-5.432a4.163,4.163,0,0,1,3.484,1.682V4.408H60V15H57.321Zm-2.8-6.864a2.82,2.82,0,0,0-2.78,3.078,2.889,2.889,0,0,0,2.78,3.155,2.846,2.846,0,0,0,2.8-3.117,2.848,2.848,0,0,0-2.8-3.119Zm-9.84,3.136V15H42.015V4.408H44.68V6.053a3.654,3.654,0,0,1,3.239-1.8V7.066h-.706c-1.58,0-2.532.612-2.532,2.658ZM33.546,15.117a6.591,6.591,0,0,1-6.8-6.807,6.606,6.606,0,0,1,6.372-6.828q.2-.006.4,0a6.48,6.48,0,0,1,5,2.07L36.561,5.53a3.35,3.35,0,0,0-3.034-1.581c-2.379,0-4.036,1.7-4.036,4.359,0,2.715,1.676,4.4,4.15,4.4a3.612,3.612,0,0,0,3.765-3.078H32.84V7.582h7.2V9.914a6.5,6.5,0,0,1-6.492,5.2ZM15.4,14.964c-3.922,0-5.14-3.04-5.14-7.112C10.263,3.818,11.481.8,15.4.8s5.141,3.019,5.141,7.054c0,4.072-1.218,7.112-5.14,7.112Zm0-11.682c-2.246,0-2.513,2.18-2.513,4.569,0,2.486.266,4.628,2.513,4.628s2.513-2.142,2.513-4.628c0-2.392-.265-4.571-2.512-4.571ZM.783,12.84,5.391,6.607H.8v-2.2H8.455V6.568L3.81,12.8h4.7V15H.783ZM76.681,15H74.016V4.408h2.666Z" transform="translate(-0.783 -0.01)"/></g></svg>',
		'lock' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;fill-rule:evenodd;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M12.923,7.5V5A4.924,4.924,0,1,0,3.077,5V7.5H1.231A1.241,1.241,0,0,0,0,8.75v10A1.241,1.241,0,0,0,1.231,20H14.769A1.241,1.241,0,0,0,16,18.75v-10A1.241,1.241,0,0,0,14.769,7.5Zm-8-2.5a3.077,3.077,0,1,1,6.154,0V7.5H4.923Zm9.231,13.125H1.846V9.375H14.154Z" transform="translate(4 2)"/></svg>',
		
	);
?>

<div class="page-login__panel new">
	<ul class="page-login-lang">
		<?php
		$langCode = Configure::read('Config.langCode');
		?>
		<li>
			<a href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn fr <?php echo ($langCode == 'fr' ? 'page-login__lang-btn--active' : ''); ?>"><?php __("FR") ?></a>
		</li>
		<li>
			<a href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn en <?php echo ($langCode == 'en' ? 'page-login__lang-btn--active' : ''); ?>"><?php __("EN") ?></a>
		</li>
		<li>
			<a href="<?php echo $this->here . Router::queryString(array('hl' => 'vi') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn vi <?php echo ($langCode == 'vi' ? 'page-login__lang-btn--active' : ''); ?>"><?php __("VI") ?></a>
		</li>
	</ul>
    <div class="page-login__logo">
		<?php echo $svg_icons['logo-zog']; ?>
		<?php echo $session->flash("auth");?>
    </div>
    <div class="page-login__flash" style="display:none">
		<?php 
		// Clear Flash message
		echo $session->flash();
		?>
	</div>
	<?php echo $this->Form->create('Employee', array('id' => 'EmployeeLoginForm', 'autocomplete' => 'off', 'url' => '/' . $query)); ?>
	
	<div class="tokent-sent-content">
		<div class="tokent-text-spam"><?php echo ($company_two_factor_auth=='send_mail' ?  __("Security code sent to your email.", true) : ( !empty($url_qrcode) ? __('Scan your QR code with Microsoft or Google Authenticator.', true) : __('Enter the code generated by Microsoft or Google Authenticator.', true)));  ?></div>
	</div>
	<?php if(!empty($url_qrcode)){ ?>
		<div class="wd-qr-code">
			<img src="<?php echo $url_qrcode ?>" alt="QRcode"/>
		</div>
	
	<?php } ?>
    <div class="page-login__input page-login__input-pass has-val">
		<label for="EmployeeOTP" ><?php echo __("OTP/CODE", true);  ?></label>
        <?php echo $svg_icons['lock']; ?>
        <?php echo $this->Form->input('otp_code', array(
                'autocomplete' => 'off',
                'tabindex' => 2,
                "div" => false,
                "label" => false,
            ))
        ?>
        
    </div>
	<div class="page-login__button">
		<button type="submit" class="page-login__login-btn btn btn--fancy"><?php __("to Validate") ?></button>
    </div>

    <a href="<?php echo $this->Html->url('/logout'); ?>" class="page-login__lost-password"><?php __("Sign out") ?></a>
		<?php 	
			$date = getdate();
			$currentYear = $date['year'];
		?>
	<?php echo $this->Form->end();?>

    <div class="page-login__copy-right">
		
        <?php echo '' . sprintf(__('Copyright © 2012-%s. <a target="_blank" href="https://www.z0gravity.com/">Version %s.</a> All rights reserved.', true),$currentYear,  $version) . ''; ?>
    </div>

</div>

<?php echo $this->Form->end();?>
<?php echo $html->script('jquery.min'); ?>
<?php echo $html->script('jquery.cookie'); ?>
<?php echo $html->script('jquery.valid'); ?>
<?php echo $html->script('jquery.md5'); ?>

