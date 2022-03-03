<?php
$query = $this->params['url']['url'] . Router::queryString(array_diff_key(
                        $this->params['url'], array('url' => '', 'ext' => '')), array());
?>
<?php 
	$svg_icons = array(
		'logo-zog' => '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="76" viewBox="0 0 96 76"><defs><style>.a{fill:none;}.l,.d{fill:#1c557d;}.c{fill:#217fc2;}.d{fill-rule:evenodd;}</style></defs><g transform="translate(24)"><g transform="translate(0 0)"><circle class="a" cx="24" cy="24" r="24" transform="translate(0 0)"/><path class="l" d="M18.128,26.4a24.173,24.173,0,0,1-4.837-.488,23.876,23.876,0,0,1-4.505-1.4A23.994,23.994,0,0,1,4.71,22.3,24.17,24.17,0,0,1,1.158,19.37c-.4-.4-.787-.816-1.158-1.242l3.411-3.411A19.166,19.166,0,0,0,18.128,21.6,19.21,19.21,0,0,0,37.179,0h4.83c.079.795.119,1.6.119,2.4a24.171,24.171,0,0,1-.488,4.837,23.88,23.88,0,0,1-1.4,4.505,24.007,24.007,0,0,1-2.213,4.077A24.129,24.129,0,0,1,31.547,22.3a24,24,0,0,1-4.077,2.213,23.879,23.879,0,0,1-4.505,1.4A24.173,24.173,0,0,1,18.128,26.4Z" transform="translate(5.872 21.6)"/></g><g transform="translate(0 0)"><circle class="a" cx="24" cy="24" r="24" transform="translate(0 0)"/><path class="c" d="M18.395,37.269A14.4,14.4,0,0,1,9.8,26.4H.119C.04,25.605,0,24.8,0,24a24.189,24.189,0,0,1,.487-4.837A23.872,23.872,0,0,1,4.1,10.581a24.076,24.076,0,0,1,10.56-8.7,23.891,23.891,0,0,1,4.505-1.4,24.238,24.238,0,0,1,9.673,0A23.862,23.862,0,0,1,37.418,4.1a24.152,24.152,0,0,1,2.31,1.773L36.317,9.283A19.191,19.191,0,0,0,4.949,21.6H9.8a14.328,14.328,0,0,1,2.261-5.651,14.445,14.445,0,0,1,6.336-5.217,14.379,14.379,0,0,1,13.656,1.328c.292.2.582.409.861.629l-3.433,3.433A9.6,9.6,0,1,0,33.3,26.4H24V21.6H38.2a14.555,14.555,0,0,1,.2,2.4,14.331,14.331,0,0,1-2.459,8.051,14.448,14.448,0,0,1-6.336,5.217,14.45,14.45,0,0,1-11.21,0Z" transform="translate(0 0)"/></g></g><g transform="translate(0 56)"><path class="d" d="M90.255,20.01H87.418L89.7,14.733,85.478,4.408h2.988l2.723,7.4,2.76-7.4h2.833Zm-10.529-8.3v-5.1H78.47v-2.2h1.256V1.789H82.41V4.408h2.361v2.2H82.41v5.124c0,.707.288,1.013,1.123,1.013h1.238V15H83.1C81.078,15,79.726,14.14,79.726,11.712ZM75.367,3.148A1.57,1.57,0,0,1,75.2.012,1.731,1.731,0,0,1,77,1.529c0,.017,0,.034,0,.051a1.578,1.578,0,0,1-1.588,1.567h-.049ZM65.373,15,61.489,4.408h2.856l2.665,8.128,2.666-8.128h2.836L68.607,15Zm-8.052-1.548a4.153,4.153,0,0,1-3.5,1.721c-2.665,0-4.8-2.2-4.8-5.507s2.133-5.432,4.817-5.432a4.163,4.163,0,0,1,3.484,1.682V4.408H60V15H57.321Zm-2.8-6.864a2.82,2.82,0,0,0-2.78,3.078,2.889,2.889,0,0,0,2.78,3.155,2.846,2.846,0,0,0,2.8-3.117,2.848,2.848,0,0,0-2.8-3.119Zm-9.84,3.136V15H42.015V4.408H44.68V6.053a3.654,3.654,0,0,1,3.239-1.8V7.066h-.706c-1.58,0-2.532.612-2.532,2.658ZM33.546,15.117a6.591,6.591,0,0,1-6.8-6.807,6.606,6.606,0,0,1,6.372-6.828q.2-.006.4,0a6.48,6.48,0,0,1,5,2.07L36.561,5.53a3.35,3.35,0,0,0-3.034-1.581c-2.379,0-4.036,1.7-4.036,4.359,0,2.715,1.676,4.4,4.15,4.4a3.612,3.612,0,0,0,3.765-3.078H32.84V7.582h7.2V9.914a6.5,6.5,0,0,1-6.492,5.2ZM15.4,14.964c-3.922,0-5.14-3.04-5.14-7.112C10.263,3.818,11.481.8,15.4.8s5.141,3.019,5.141,7.054c0,4.072-1.218,7.112-5.14,7.112Zm0-11.682c-2.246,0-2.513,2.18-2.513,4.569,0,2.486.266,4.628,2.513,4.628s2.513-2.142,2.513-4.628c0-2.392-.265-4.571-2.512-4.571ZM.783,12.84,5.391,6.607H.8v-2.2H8.455V6.568L3.81,12.8h4.7V15H.783ZM76.681,15H74.016V4.408h2.666Z" transform="translate(-0.783 -0.01)"/></g></svg>',
		'mail'=> '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;fill-rule:evenodd;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M18.125,0H1.875A1.94,1.94,0,0,0,0,2V12a1.94,1.94,0,0,0,1.875,2h16.25A1.94,1.94,0,0,0,20,12V2A1.94,1.94,0,0,0,18.125,0ZM2.5,1.334h15a1.178,1.178,0,0,1,.5.115L10.522,8.553a.777.777,0,0,1-.474.164.738.738,0,0,1-.482-.182L2.2,1.376A1.173,1.173,0,0,1,2.5,1.334ZM1.25,2.667a1.406,1.406,0,0,1,.058-.382L6.224,7.062,1.309,11.72a1.406,1.406,0,0,1-.059-.387Zm16.25,10H2.5a1.178,1.178,0,0,1-.262-.03L7.161,7.972l1.6,1.558a1.953,1.953,0,0,0,2.548.028l1.6-1.518,4.85,4.6A1.178,1.178,0,0,1,17.5,12.667Zm1.25-1.333a1.406,1.406,0,0,1-.059.387L13.858,7.141l4.877-4.63a1.367,1.367,0,0,1,.015.156Z" transform="translate(2 5)"/></svg>',
		'user' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7B7B7B;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M122.491,191.893a.994.994,0,0,0,1.1-.864c.475-4.1,1.869-6.116,4.66-6.754a.977.977,0,0,0,.064-1.894,4.338,4.338,0,0,1-3.14-4.166,4.428,4.428,0,0,1,8.854,0,4.338,4.338,0,0,1-3.14,4.166.977.977,0,0,0,.064,1.894c2.792.638,4.186,2.658,4.661,6.754a.992.992,0,0,0,.99.87,1.04,1.04,0,0,0,.114-.006.985.985,0,0,0,.878-1.086c-.3-2.564-1.056-5.914-4.027-7.616a6.244,6.244,0,0,0,2.456-4.977,6.423,6.423,0,0,0-12.845,0,6.243,6.243,0,0,0,2.456,4.976c-2.971,1.7-3.73,5.051-4.027,7.616A.985.985,0,0,0,122.491,191.893Z" transform="translate(-117.606 -169.899)"/></svg>',
		'lock' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.a{fill:none;}.b{fill:#7b7b7b;fill-rule:evenodd;}</style></defs><rect class="a" width="24" height="24"/><path class="b" d="M12.923,7.5V5A4.924,4.924,0,1,0,3.077,5V7.5H1.231A1.241,1.241,0,0,0,0,8.75v10A1.241,1.241,0,0,0,1.231,20H14.769A1.241,1.241,0,0,0,16,18.75v-10A1.241,1.241,0,0,0,14.769,7.5Zm-8-2.5a3.077,3.077,0,1,1,6.154,0V7.5H4.923Zm9.231,13.125H1.846V9.375H14.154Z" transform="translate(4 2)"/></svg>',
		'sso' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><g transform="translate(-336 -688)"><rect class="fill-none" width="48" height="48" transform="translate(336 688)"/><path class="sso-b" d="M-2580-2091a23.842,23.842,0,0,1-16.97-7.03A23.843,23.843,0,0,1-2604-2115a23.844,23.844,0,0,1,7.029-16.971A23.842,23.842,0,0,1-2580-2139a23.843,23.843,0,0,1,16.971,7.029A23.843,23.843,0,0,1-2556-2115a23.843,23.843,0,0,1-7.03,16.971A23.843,23.843,0,0,1-2580-2091Zm0-46a22.025,22.025,0,0,0-22,22,22.025,22.025,0,0,0,22,22,22.025,22.025,0,0,0,22-22A22.025,22.025,0,0,0-2580-2137Z" transform="translate(2940 2827)"/><path class="sso-c" d="M11.22,16H1.58A1.558,1.558,0,0,1,0,14.466v-6.6A1.558,1.558,0,0,1,1.58,6.331H1.7V4.538A4.613,4.613,0,0,1,6.372,0a4.613,4.613,0,0,1,4.673,4.538V6.331h.175A1.558,1.558,0,0,1,12.8,7.866v6.6A1.558,1.558,0,0,1,11.22,16ZM5.288,13.775a.117.117,0,0,0,.025.1.122.122,0,0,0,.094.043l1.95.012h0a.12.12,0,0,0,.122-.118.1.1,0,0,0,0-.031L6.95,11.326a1.293,1.293,0,0,0,.778-1.174,1.329,1.329,0,0,0-2.656,0,1.283,1.283,0,0,0,.737,1.155ZM8.746,4.538a2.376,2.376,0,0,0-4.75,0V6.331h4.75Z" transform="translate(354 698)"/><path class="sso-d" d="M-8.645-4.24a.826.826,0,0,0-.318-.7,4.151,4.151,0,0,0-1.144-.513,8.07,8.07,0,0,1-1.309-.532,2.158,2.158,0,0,1-1.314-1.93,1.892,1.892,0,0,1,.352-1.126,2.3,2.3,0,0,1,1.012-.773,3.778,3.778,0,0,1,1.481-.278,3.439,3.439,0,0,1,1.473.3,2.351,2.351,0,0,1,1,.853A2.247,2.247,0,0,1-7.05-7.683H-8.639a1.055,1.055,0,0,0-.334-.832,1.364,1.364,0,0,0-.938-.3,1.456,1.456,0,0,0-.906.249.787.787,0,0,0-.323.655.752.752,0,0,0,.379.636,4.259,4.259,0,0,0,1.115.481A4.8,4.8,0,0,1-7.67-5.769,2.044,2.044,0,0,1-7.05-4.25a1.88,1.88,0,0,1-.758,1.586,3.294,3.294,0,0,1-2.04.575,3.907,3.907,0,0,1-1.621-.329,2.57,2.57,0,0,1-1.115-.9,2.323,2.323,0,0,1-.384-1.326h1.595q0,1.289,1.526,1.289a1.474,1.474,0,0,0,.885-.233A.759.759,0,0,0-8.645-4.24Zm6.67,0a.826.826,0,0,0-.318-.7,4.151,4.151,0,0,0-1.144-.513,8.07,8.07,0,0,1-1.309-.532,2.158,2.158,0,0,1-1.314-1.93,1.892,1.892,0,0,1,.352-1.126,2.3,2.3,0,0,1,1.012-.773,3.778,3.778,0,0,1,1.481-.278,3.439,3.439,0,0,1,1.473.3,2.351,2.351,0,0,1,1,.853A2.247,2.247,0,0,1-.38-7.683H-1.969A1.055,1.055,0,0,0-2.3-8.515a1.364,1.364,0,0,0-.938-.3,1.456,1.456,0,0,0-.906.249.787.787,0,0,0-.323.655.752.752,0,0,0,.379.636,4.259,4.259,0,0,0,1.115.481A4.8,4.8,0,0,1-1-5.769,2.044,2.044,0,0,1-.38-4.25a1.88,1.88,0,0,1-.758,1.586,3.294,3.294,0,0,1-2.04.575A3.907,3.907,0,0,1-4.8-2.419a2.57,2.57,0,0,1-1.115-.9A2.323,2.323,0,0,1-6.3-4.646H-4.7q0,1.289,1.526,1.289a1.474,1.474,0,0,0,.885-.233A.759.759,0,0,0-1.974-4.24ZM7.032-5.913A4.738,4.738,0,0,1,6.63-3.9,3.052,3.052,0,0,1,5.477-2.56a3.165,3.165,0,0,1-1.719.471,3.187,3.187,0,0,1-1.711-.465A3.081,3.081,0,0,1,.881-3.884,4.6,4.6,0,0,1,.463-5.871v-.385A4.713,4.713,0,0,1,.873-8.28a3.077,3.077,0,0,1,1.16-1.342,3.169,3.169,0,0,1,1.714-.468,3.169,3.169,0,0,1,1.714.468A3.077,3.077,0,0,1,6.622-8.28a4.7,4.7,0,0,1,.411,2.019ZM5.422-6.266a3.3,3.3,0,0,0-.434-1.861,1.412,1.412,0,0,0-1.24-.636,1.412,1.412,0,0,0-1.234.628,3.263,3.263,0,0,0-.44,1.842v.38a3.342,3.342,0,0,0,.434,1.85,1.408,1.408,0,0,0,1.25.658,1.4,1.4,0,0,0,1.229-.634,3.323,3.323,0,0,0,.434-1.848Z" transform="translate(362.968 728.09)"/></g></svg>',
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
	<?php echo $this->Form->create('Employee', array('id' => 'EmployeeLoginForm', 'autocomplete' => 'off', 'url' => '/' . $query)); ?>
    <div class="page-login__input has-val">
		<label for="EmployeeEmail" ><?php echo __("New login", true);  ?></label>
		<?php echo $svg_icons['user']; ?>
        <?php echo $this->Form->input('email', array(
                'autocomplete' => 'off',
                'tabindex' => 1,
                "class" => "placeholder required email login-email",
                "div" => false,
                "label" => false,
            ))
        ?>
    </div>
    <div class="page-login__input page-login__input-pass has-val">
		<label for="EmployeePassword" ><?php echo __("Password", true);  ?></label>
        <?php echo $svg_icons['lock']; ?>
        <?php echo $this->Form->input('password', array(
                'autocomplete' => 'off',
                'tabindex' => 2,
                "div" => false,
                "label" => false,
            ))
        ?>
        
    </div>
	<div class="page-login__button">
		<button type="submit" class="page-login__login-btn btn btn--fancy"><?php __("To login") ?></button>
    </div>
	<?php if( count($sso_companies)){
		$sso_href = 'javascript:void(0);';
		$sso_class = '';
		if(  count($sso_companies) == 1){
			$sso_href = $this->Html->url(array(
				'controller' => 'sso_logins',
				'action' => 'login',
				$sso_companies[0],
				'?' => array('sso' => 1)
			));
			$sso_class = ' one-company';
		}
		?>
		<div class="page-login__button text-center">
			<a href="<?php echo $sso_href;?>" class="sso-login-btn<?php echo $sso_class;?>" id="sso-login-btn">
				<span class="hvr-ripple-in-custom sso-svg-cont"><?php echo $svg_icons['sso']; ?></span>
				<span class="sso-text">SSO SAML2</span>
			</a>
		</div>
	<?php } ?> 
    <a class="page-login__lost-password"><?php __("Forgot your password?") ?></a>
		<?php 	
			$date = getdate();
			$currentYear = $date['year'];
		?>
	<?php echo $this->Form->end();?>
	<?php echo $this->Form->create('EmployeeRecovery', array('id' => 'EmployeeRecoveryForm', 'url' => '/recovery', 'style' => 'display: none' )); ?>
		<div class="page-login__logo">
			<h4><?php __("Reset password") ?></h4>
			<span><?php __("An email will be sent to you") ?></span>
		</div>
		<div class="page-login__input input-recovery">
			<label for="EmployeeRecoveryEmail"><?php __("Your e-mail address") ?></label>
			<?php echo $svg_icons['mail']; ?>
			<?php echo $this->Form->input('email', array(
					'autocomplete' => 'off',
					'tabindex' => 1,
					"class" => "placeholder required email",
					'name' =>"data[Employee][email]",
					"div" => false,
					"label" => false
				))
			?>
			
		</div>
		<button onclick="javascript:fsubmit();" class="page-login__login-btn btn btn--fancy"><?php __("Send") ?></button>
		<a class="page-recovery__lost-password"><?php __("Back To Home") ?></a>
		
	<?php echo $this->Form->end();?>
    <div class="page-login__copy-right">
		
        <?php echo '' . sprintf(__('Copyright © 2012-%s. <a target="_blank" href="https://www.z0gravity.com/">Version %s.</a> All rights reserved.', true),$currentYear,  $version) . ''; ?>
    </div>

</div>
<div id="sso_select_company_popup" class="wd-full-popup sso_select_company_popup" style="display: none">
	<div class="wd-popup-inner">
		<div class="template-popup loading-mark wd-popup-container">
			<div class="wd-popup-head clearfix">
				<h4 class="active"><?php __('Select company'); ?></h4>
				<a href="javascript:void(0)" class="wd-close-popup" onclick="cancel_popup(this);"><img title="close" src="<?php echo $this->Html->url('/img/new-icon/close.png');?>"></a>
			</div>
			<div class="template-popup-content wd-popup-content">
				<div class="wd-popup-content-inner">
				<?php 
					foreach( $sso_companies as $companyID){
						echo $this->Html->link($all_companies[$companyID], array(
							'controller' => 'sso_logins',
							'action' => 'login',
							$companyID,
							'?' => array('sso' => 1)
						),array(
							'class' => 'sso-company sso-company-'.$companyID,
						)); 
					} 
				?>
				</div>
				<div class="wd-row wd-submit-row">
					<div class="wd-col-xs-12">
						<div class="wd-submit">
							<a class="btn-form-action btn-cancel" id="vs_cancel_button" href="javascript:void(0);" onclick="cancel_popup(this);">
								<span><?php __('Cancel');?></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end();?>
<?php echo $html->script('jquery.min'); ?>
<?php echo $html->script('jquery.cookie'); ?>
<?php echo $html->script('jquery.valid'); ?>
<?php echo $html->script('jquery.md5'); ?>
<script type="text/javascript">

	$('.page-login__lost-password').on('click', function(){
		$('#EmployeeRecoveryForm').slideDown();
		$('#EmployeeLoginForm').slideUp();
	});
	$('.page-recovery__lost-password').on('click', function(){
		$('#EmployeeRecoveryForm').slideUp();
		$('#EmployeeLoginForm').slideDown();
	});
	function show_full_popup(elm, args){
		var isTablet = isTablet || false;
		var isMobile = isMobile || false;
		if( $(elm).hasClass('wd-full-popup')) {
			var _popup = $(elm);
			if(typeof args != 'object') args = {};
			if( 'title' in args) _popup.find('.wd-popup-head h4').html(args.title);			
			if( 'width' in args) _popup.find('.wd-popup-container').width( Math.min(args.width, $(window).width() - 30 ) );
			else _popup.find('.wd-popup-container').width( (isTablet || isMobile) ?  320 : 580 );
			if( 'height' in args) _popup.find('.wd-popup-content').height(args.height);
			else _popup.find('.wd-popup-content:first').css( 'max-height', ( $(window).height() - 70 - 80 ) ); // wd-popup-head cao 70
			$('#layout').addClass('wd-popup-ontop');
			$(elm).fadeIn(300);
			$(elm).find('input, select, textarea').trigger('change');
			var id = $(elm).prop('id');
			if( id ){
				var _id = id.replace(/-/g, '_');
				var _function = _id + '_showed' ;
				// console.log( _function);
				if( typeof window[_function] == 'function'){
					window[_function](id);
				}
			}		
		}
	}
	function cancel_popup(elm){
		$(elm).closest('.wd-full-popup').fadeOut(300);
		var _forms = $(elm).closest('.wd-full-popup').find('.form-style-2019');
		if( _forms.length) $.each( _forms, function( ind, _form){
			$(_form)[0].reset();
		});
		$(elm).closest('.wd-full-popup').find('.loading-mark').removeClass('loading');
		$('#layout').removeClass('wd-popup-ontop');
		var id = $(elm).closest('.wd-full-popup').prop('id');
		if( id ){
			var _id = id.replace(/-/g, '_');
			var _function = 'cancel_popup_' + _id ;
			// console.log(_id, id, _function);
			if( typeof window[_function] == 'function'){
				window[_function](id);
			}
		}
	}
	$(window).ready(function(){
		$('.page-login__input input').trigger('click');
		var history_pass = $('#EmployeePassword').val();
		var history_email = $('#EmployeeEmail').val();
		if(history_pass.length || history_email.length){
			$('.page-login__input').addClass('has-val');
		}
		
		$('.page-login__input input').focus(function(){
			if(!$(this).val()){
				$(this).closest('.page-login__input').addClass('has-val');
			}
		});
		$('.page-login__input input').focusout(function(){
			if(!$(this).val()){
				$(this).closest('.page-login__input').removeClass('has-val');
			}
		});
		$('#sso-login-btn').on('click', function(){
			if( $('#sso-login-btn').hasClass('one-company')) return;
			show_full_popup('#sso_select_company_popup', {width: 420});
		});
	});
</script>
