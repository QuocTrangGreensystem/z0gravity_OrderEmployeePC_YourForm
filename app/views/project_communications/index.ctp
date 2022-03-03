<?php
	App::import("vendor", "str_utility");
	$str_utility = new str_utility();
	App::import("vendor", "str_utility");
	$str_utility = new str_utility();
	echo $this->Html->css(array(
		'preview/layout',
		'layout_2019',
		'project_article',
		'slick',
		'slick-theme',
	));
	echo $html->script(array(
		'slick.1.5.9.min',
		'common'
	)); 
	
	$display_len = 160;
	$articles = Set::sort($articles, '{n}.ProjectCommunication.public_date', 'desc');
	$icons = array(
		'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 80 80">
			<g transform="translate(-56 -32)"><rect class="svg-a" width="80" height="80" transform="translate(56 32)"></rect><path class="svg-b" d="M147.612,20.638h-7.529a33.272,33.272,0,0,1,.17,3.335A32.5,32.5,0,0,1,86.5,48.558l-5.308,5.317a39.993,39.993,0,0,0,66.559-29.9C147.748,22.85,147.7,21.735,147.612,20.638Z" transform="translate(-11.748 48.027)"></path><path class="svg-c" d="M119.6,81.33a25,25,0,0,0,25-25,25.338,25.338,0,0,0-.221-3.335H119.6v6.67h17.184A17.5,17.5,0,1,1,130.2,42.4l5.334-5.342A25,25,0,0,0,94.829,52.993H87.275a32.5,32.5,0,0,1,53.586-21.251l5.308-5.317a39.993,39.993,0,0,0-66.559,29.9c0,1.123.043,2.237.136,3.335H94.829A25,25,0,0,0,119.6,81.33Z" transform="translate(-23.61 15.672)"></path></g>
			</svg>',
		'embed' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" style="top:10px; position:relative;">
			  <rect id="Canvas" width="18" height="18" fill="#ff13dc" opacity="0"/>
			  <path class="svg-b" d="M8.226,14.473a.5.5,0,0,1-.483.371H7.227a.5.5,0,0,1-.472-.666L9.786,2.693a.5.5,0,0,1,.483-.37h.5a.5.5,0,0,1,.472.665Z" fill="#707070"/>
			  <path class="svg-b" d="M17.746,9.53l-4.095,4.16a.5.5,0,0,1-.713,0l-.446-.453a.5.5,0,0,1,0-.7L15.971,9,12.492,5.464a.5.5,0,0,1,0-.7l.446-.454a.5.5,0,0,1,.713,0l4.095,4.16A.76.76,0,0,1,17.746,9.53Z" fill="#707070"/>
			  <path  class="svg-b" d="M.254,8.47l4.1-4.161a.5.5,0,0,1,.713,0l.446.454a.5.5,0,0,1,0,.7L2.029,9l3.479,3.535a.5.5,0,0,1,0,.7l-.446.453a.5.5,0,0,1-.713,0L.254,9.53a.76.76,0,0,1,0-1.06Z" fill="#707070"/>
			</svg>',
		'email' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M0,9.92A1.307,1.307,0,0,0,1.334,11.2H14.666A1.307,1.307,0,0,0,16,9.92V1.28A1.307,1.307,0,0,0,14.666,0H1.334A1.307,1.307,0,0,0,0,1.28ZM4.906,5.61,1.857,2.663a.6.6,0,0,1,0-.88.666.666,0,0,1,.916,0L7.64,6.461a.528.528,0,0,0,.714,0l4.873-4.679a.667.667,0,0,1,.917,0,.6.6,0,0,1,0,.88L11.09,5.61l3.054,2.928a.6.6,0,0,1,0,.88.668.668,0,0,1-.917,0l-3.05-2.925s-.954.931-1.126,1.1A1.541,1.541,0,0,1,8,8a1.52,1.52,0,0,1-1.057-.419C6.766,7.414,5.82,6.493,5.82,6.493L2.773,9.418a.667.667,0,0,1-.916,0,.6.6,0,0,1,0-.88Z" transform="translate(4 6)"/></svg>',
		'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M7.185,0V2.577s-1.821-.19-2.278.538a7.64,7.64,0,0,0-.123,2.4H7.2C7,6.49,6.85,7.152,6.7,8H4.77v8H1.424c0-2.466,0-5.343,0-7.968H0V5.512H1.408c.072-1.84.1-3.663.977-4.592C3.371-.123,4.311,0,7.185,0Z" transform="translate(8 4.002)"/>',
		'twitter' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M10.883,0a3.4,3.4,0,0,1,2.594,1.015A5.367,5.367,0,0,0,15.159.452l.408-.22A3.231,3.231,0,0,1,14.5,1.75a1.579,1.579,0,0,1-.36.252V2.01A6.226,6.226,0,0,0,16,1.514v.008a5.251,5.251,0,0,1-1.113,1.259l-.513.4a9.131,9.131,0,0,1-.152,2.078,9.092,9.092,0,0,1-6.43,7.154,9.973,9.973,0,0,1-4.645.189,12.112,12.112,0,0,1-1.93-.622,7.769,7.769,0,0,1-.929-.472L0,11.335a5.429,5.429,0,0,0,1.049.04c.323-.052.639-.038.937-.1a6.8,6.8,0,0,0,1.97-.7A3.676,3.676,0,0,0,4.845,10a2.807,2.807,0,0,1-.977-.173,3.273,3.273,0,0,1-2.082-2.07c.322.034,1.249.117,1.466-.063a2.432,2.432,0,0,1-1.073-.425A3.1,3.1,0,0,1,.633,4.5l.336.157a3.6,3.6,0,0,0,.689.19,1.013,1.013,0,0,0,.449.039H2.09c-.166-.19-.435-.316-.6-.52A3.272,3.272,0,0,1,.753,1.428,3.717,3.717,0,0,1,1.1.586l.016.007a1.992,1.992,0,0,0,.3.339,5.988,5.988,0,0,0,.977.92A9.731,9.731,0,0,0,6.406,3.765a6.335,6.335,0,0,0,1.474.19A2.963,2.963,0,0,1,7.9,2.435,3.2,3.2,0,0,1,9.754.263a3.957,3.957,0,0,1,.729-.22Z" transform="translate(4 6.006)"/>',
		'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect class="svg-a" width="24" height="24"/><path class="svg-b" d="M12.438,14.4V9.161c0-1.074-.33-2.149-1.669-2.149a1.929,1.929,0,0,0-1.9,2.176V14.4H5.3V4.807H8.87V6.1A3.7,3.7,0,0,1,12.108,4.5C13.59,4.5,16,5.158,16,8.946V14.4ZM2,3.385A1.861,1.861,0,0,1,0,1.693,1.862,1.862,0,0,1,2,0a1.863,1.863,0,0,1,2,1.693A1.862,1.862,0,0,1,2,3.385ZM3.778,14.4H.2V4.807H3.778Z" transform="translate(4 4.999)"/>',
	);
	$i18ns = array(
		'public_info' => __('Published on %1$s, by %2$s', true)
	);
	for ($m=1; $m<=12; $m++) {
		$month = date('M', mktime(0,0,0,$m, 1, 2000));
		$i18ns[$m] = __($month,true);
		$i18ns[$month] = __($month,true);
	}
	$link = $this->Html->url();
	$link = urlencode($link);
	$socials = array(
		'mail' => array(
			'url' => 'mailto:?body='.$link,
			'text' => __('Send mail', true),
			'icon' => $icons['email']
		),
		'facebook' => array(
			'url' => 'https://www.facebook.com/sharer.php?u='.$link,
			'text' => __('Share on Facebook', true),
			'icon' => $icons['facebook'],
		),
		'twitter' => array(
			'url' => 'https://twitter.com/intent/tweet?url='.$link,
			'text' => __('Share on Twitter', true),
			'icon' => $icons['twitter'],
		),
		'linkedin' => array(
			'url' => 'https://www.linkedin.com/cws/share?url='.$link,
			'text' => __('Share on Linkedin', true),
			'icon' => $icons['linkedin'],
		),
	);
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="wd-list-article article-container">
						<?php /* if( !empty($companyConfigs['communication_title'])){?>
							<div class="wd-grid-title block">
								<h2><?php echo $companyConfigs['communication_title'];?></h2>
							</div>
						<?php } */?> 
						<div id="message-place">
							<?php
							echo $this->Session->flash();
							?>
						</div>
						<?php if( !empty($articles)){ ?>
							<div class="wd-blog-info block custom-background-color">
								<div class="text-left">
									<a href="https://z0gravity.com" target="_blank" title="z0gravity.com" class="logo"> <?php echo $icons['logo'];?></a>
									<?php if( !empty($companyConfigs['communication_title'])){?>
										<h2 class="blog-grid-title"><?php echo $companyConfigs['communication_title'];?></h2>
									<?php } ?> 
								</div>
								<div class="text-right">
									<div class="social-share">
										<ul>
										<?php
										foreach($socials as $social){
											$url = $social['url'];
											$icon = $social['icon'];
											$text = $social['text'];
											echo "<li class='social'><a href='{$url}' target='_blank' title='{$text}'>{$icon}</a></li>";
										} 
										?> 
										</ul>
									</div> 
								</div>
							</div>
							<div id="article-slider">
								<div class="slides">
									<?php foreach($articles as $k=>$article){
									$blog_item = $article['ProjectCommunication'];
									$attachment = $this->Html->url(array(
										'controller' => $this->params['controller'],
										'action' => 'attachment',
										$blog_item['project_id'],
										$blog_item['id'],
										'thumb'
										));
									$url = $this->Html->url(array(
										'controller' => $this->params['controller'],
										'action' => 'view',
										$blog_item['public_key']
										));
									$content = strip_tags($blog_item['content']);
									$content = (strlen($content) > $display_len) ? substr($content, 0, $display_len).' (...)' : $content ;
									?>
									<div class="slick-item article-item item-<?php echo $k+1;?>" style="width: 340px;">
										<div class="article-image block"><a href="<?php echo $url;?>"> <img src="<?php echo $attachment;?>" alt="Image"/></a></div>
										<div class="article-content-block">
											<h4 class="article-title block"><a href="<?php echo $url;?>"><?php echo $blog_item['communication_title']?></a></h4>
											<div class="article-content"><?php echo $content;?></div>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(window).ready(function(){
	$('#article-slider .slides').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 3,
		centerMode: false,
		variableWidth: true
	});
});
</script>
