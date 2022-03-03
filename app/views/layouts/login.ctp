<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="page-login">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management software | z0 GRAVITY. z0 GRAVITY is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="z0 GRAVITY, Project Management, z0 GRAVITY, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
		<!-- Login -file -->
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
        <title><?php __("z0 GRAVITY :: Login") ?></title>
        <?php echo $html->css(array('main', 'common_z0g')); ?>
        <?php echo $html->css('simple-line-icons/css/simple-line-icons');?>
        <?php echo $this->element("style/custom_css");  ?>
        <?php echo $this->Html->css('preview/login');
			echo $html->css('slick'); 
			echo $html->css('slick-theme');  
		?>
    </head>
    <?php
        $langCode = Configure::read('Config.langCode');
        // login background image
        
        $link = 'img/login_bg_pic.png';
		$link_full = '';
        if(!empty($bg_login['Color']['attachment'])) {
            // use for full HD
			// improve late
			$link = $this->Html->url(array('controller' => 'colors', 'action' => 'attachment', 0 , 'attachment' , $bg_login['Color']['id'], 'large', '?' => array('sid' => 'e4274367e6717bd6be99deb998f5f23d')), true);
			
			// get origin
            $link = $this->Html->url(array('controller' => 'colors', 'action' => 'attachment', 0 , 'attachment' , $bg_login['Color']['id'], '?' => array('sid' => 'e4274367e6717bd6be99deb998f5f23d')), true);
        }
		if(!empty($logo_client['logo']['logo_client'])) {
            // get origin
            $logo_client = $this->Html->url(array('controller' => 'colors', 'action' => 'logo_client' , $logo_client['logo']['id'], 'thumbnail', '?' => array('sid' => 'e4274367e6717bd6be99deb998f5f23d')), true);
        }
        $src = 'url('.$link.') no-repeat left center';
		$has_text_about = (!empty($text_about) && (!empty($text_about['first_text']) || !empty($text_about['last_text']))) ? 'has_text_about' : '';
		$has_testimonial = (!empty($company_testimonial)) ? 'has_testimonial' : '';
		
		$svg_icon = array(
			'light_icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a{fill:none;}.light_b{fill:#217fc2;stroke:rgba(0,0,0,0);stroke-miterlimit:10;}</style></defs><rect class="a" width="40" height="40"/><path class="light_b" d="M13.862,33.827V32.341a3.514,3.514,0,0,1-1.242-2.683,4.75,4.75,0,0,0-1.613-3.531A10.667,10.667,0,0,1,17.564,7.511a10.436,10.436,0,0,1,7.75,2.959A10.7,10.7,0,0,1,24.907,26.2a4.565,4.565,0,0,0-1.527,3.455,3.514,3.514,0,0,1-1.242,2.683v1.486a4.138,4.138,0,1,1-8.276,0Zm2.007,0a2.131,2.131,0,1,0,4.263,0v-.661H15.869ZM17.645,9.533a8.64,8.64,0,0,0-5.309,15.078,6.782,6.782,0,0,1,2.29,5.047A1.482,1.482,0,0,0,16.1,31.143h3.8a1.481,1.481,0,0,0,1.473-1.485,6.593,6.593,0,0,1,2.221-4.985,8.67,8.67,0,0,0,.33-12.743A8.445,8.445,0,0,0,18,9.525Q17.824,9.525,17.645,9.533Zm13.974,9.629a1.012,1.012,0,0,1,0-2.023H35a1.012,1.012,0,0,1,0,2.023ZM1,19.162a1.012,1.012,0,0,1,0-2.023H4.381a1.012,1.012,0,0,1,0,2.023ZM28.926,11.79a1.017,1.017,0,0,1,.367-1.382l2.925-1.7a1,1,0,0,1,1.371.37,1.016,1.016,0,0,1-.367,1.382L30.3,12.16a.991.991,0,0,1-.5.136A1,1,0,0,1,28.926,11.79ZM5.7,12.16l-2.925-1.7a1.016,1.016,0,0,1-.367-1.382,1,1,0,0,1,1.37-.37l2.925,1.7a1.017,1.017,0,0,1,.367,1.382,1,1,0,0,1-.87.506A.991.991,0,0,1,5.7,12.16Zm18.6-5.027a1.016,1.016,0,0,1-.367-1.382L25.629,2.8A1,1,0,0,1,27,2.432a1.016,1.016,0,0,1,.367,1.382l-1.689,2.95a1,1,0,0,1-1.37.37Zm-13.986-.37L8.633,3.814A1.016,1.016,0,0,1,9,2.432a1,1,0,0,1,1.371.37l1.689,2.95a1.017,1.017,0,0,1-.367,1.382,1,1,0,0,1-1.371-.37ZM17,4.418V1.012a1,1,0,1,1,2.007,0V4.418a1,1,0,1,1-2.007,0Z" transform="translate(2 1)"/></svg>',
		);
        ?>
        <style>
            .page-login:after{
                background: <?php echo $src; ?>;
                background-size: cover;
           }

        </style>

    <body class="<?php echo $langCode; ?>">
		<?php if(!empty($logo_client)):?>
			<div class="page-login__logo-client">
				<img src = "<?php echo $logo_client; ?>" alt="Logo client" />
			</div>
		<?php endif; ?>
        <main class="page-login__main-holder page-login__main-holder-new <?php echo $has_text_about . ' '. $has_testimonial; ?>">
			<div class="wd-form-login">
				<?php echo $content_for_layout; ?>
			</div>
			<?php if(!empty($company_testimonial)){ ?>
			<div class="wd-slide-login">
				<div class="wd-testimonial-company" >
					<div class="wd-testimonial-slide">
						<div class="slides">
							<?php 
							foreach ($company_testimonial as $key => $testimonial) {?>
								<div class= "testimonial-item">
									<?php echo $svg_icon['light_icon']; ?>
									<div class="testimonial-title"><?php echo $testimonial['value']; ?></div>
									<div class="testimonial-content"><?php echo $testimonial['content']; ?></div>
								</div>	
							<?php }	?>
						</div>
					</div>
				</div>
			</div>
			<?php }	?>
        </main>
		<div class="wd-about-company">
			<?php 	
				if(!empty($text_about) && !empty($text_about['first_text'])) echo '<h2>'. $text_about['first_text'] .'</h2>';
				if(!empty($text_about) && !empty($text_about['last_text'])) echo '<h4>'. $text_about['last_text'] .'</h4>';
			?>
		</div>
		<?php echo $html->script(array(
				'newDesign/webfontloader',
				'newDesign/picturefill',
				'newDesign/webfonts',
				'slick.min',
			)); 
		?>
		<script>
			document.createElement( "picture" );
			var heighLang = $(window).height() - 290;
			$('.wd-login-lang').css({
				height: heighLang
			});
			$(window).resize(function(){
				var heighLang = $(window).height() - 290;
				$('.wd-login-lang').css({
					height: heighLang
				});
			});
			$('.btn-open-login').on('click', function(e){
				$(this).closest('.wd-about-login').toggleClass('open');
			});
			$('body').on('click', function(e){
				
				if(!($(e.target).hasClass('.wd-about-login') || $('.wd-about-login').find(e.target).length)){
					$('.wd-about-login').removeClass('open');
				}
			});
			$('.wd-testimonial-slide .slides').slick({
				slidesToShow: 1,
				infinite: false,
				dots: true,
				arrows: false,
			});
			var flashtimeout=0;
			$(window).ready(function(){
				flashtimeout = setTimeout(function(){
					$('#flashMessage').slideUp(300, function(){
						$(window).trigger('resize');
					});
				}, 3000)
			});
		</script>
		
    </body>
</html>