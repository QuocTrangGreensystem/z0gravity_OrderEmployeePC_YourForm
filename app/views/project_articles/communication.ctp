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
	echo $html->script('slick.min'); 
	
	$display_len = 200;
	// debug( $articles);
	$articles = Set::sort($articles, '{n}.ProjectArticle.public_date', 'desc');
	// debug( $articles);
	// exit;
	$i18ns = array(
		'public_info' => __('Published on %1$s, by %2$s', true)
	);
	for ($m=1; $m<=12; $m++) {
		$month = date('M', mktime(0,0,0,$m, 1, 2000));
		$i18ns[$m] = __($month,true);
		$i18ns[$month] = __($month,true);
	}
	$link = $this->Html->url();
	$socials = array(
		'mail' => array(
			'url' => 'mailto:?subject='.$projectName['Project']['project_name'].'&body='.$link,
			'text' => __('Send mail', true),
			'icon' => '<i class="icon-envelope"></i>'
		),
		'facebook' => array(
			'url' => 'https://www.facebook.com/sharer.php?u='.$link,
			'text' => __('Share on Facebook', true),
			'icon' => '<i class="icon-social-facebook"></i>'
		),
		'twitter' => array(
			'url' => 'https://twitter.com/intent/tweet?url='.$link,
			'text' => __('Share on Twitter', true),
			'icon' => '<i class="icon-social-twitter"></i>'
		),
		'linkedin' => array(
			'url' => 'https://www.linkedin.com/cws/share?url='.$link,
			'text' => __('Share on Linkedin', true),
			'icon' => '<i class="icon-social-linkedin"></i>'
		),
	);
	$icons = array(
		'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 80 80">
			<g transform="translate(-56 -32)"><rect class="svg-a" width="80" height="80" transform="translate(56 32)"></rect><path class="svg-b" d="M147.612,20.638h-7.529a33.272,33.272,0,0,1,.17,3.335A32.5,32.5,0,0,1,86.5,48.558l-5.308,5.317a39.993,39.993,0,0,0,66.559-29.9C147.748,22.85,147.7,21.735,147.612,20.638Z" transform="translate(-11.748 48.027)"></path><path class="svg-c" d="M119.6,81.33a25,25,0,0,0,25-25,25.338,25.338,0,0,0-.221-3.335H119.6v6.67h17.184A17.5,17.5,0,1,1,130.2,42.4l5.334-5.342A25,25,0,0,0,94.829,52.993H87.275a32.5,32.5,0,0,1,53.586-21.251l5.308-5.317a39.993,39.993,0,0,0-66.559,29.9c0,1.123.043,2.237.136,3.335H94.829A25,25,0,0,0,119.6,81.33Z" transform="translate(-23.61 15.672)"></path></g>
			</svg>',
	);
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="wd-list-article article-container">
						<div class="wd-title">
							<h1> <?php echo $projectName['Project']['project_name'];?></h1>
						</div>
						<div id="message-place">
							<?php
							echo $this->Session->flash();
							?>
						</div>
						<?php if( !empty($articles)){ 
							$article = $articles[0]; ?>
							<div class="wd-blog-info block custom-background-color">
								<div class="text-left">
									<span class="logo">
										<?php echo $icons['logo'];?>
									</span>
									<span class="public-info">
										<?php if( !empty($article['ProjectArticle'])){
											$public_date = strtotime( $article['ProjectArticle']['public_date'] );
											$public_date = date('d', $public_date) . ' ' . __(date('M', $public_date), true) . ' ' . date('Y',$public_date);
											echo sprintf( __('Published on %1$s, by %2$s', true) , $public_date , $article['Employee']['fullname']);
										}?>
									</span>
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
						<?php } ?>
						<div id="article-slider">
							<div class="slides clearfix">								
								<?php foreach($articles as $k=>$article){
								$blog_item = $article['ProjectArticle'];
								$attachment = $this->Html->url(array(
									'controller' => $this->params['controller'],
									'action' => 'attachment',
									$project_id,
									$blog_item['id']
									));
								$url = $this->Html->url(array(
									'controller' => $this->params['controller'],
									'action' => 'view',
									$blog_item['id']
									));
								$content = (strlen($blog_item['content']) > $display_len) ? substr($blog_item['content'], 0, $display_len).' (...)' : $blog_item['content'] ;
								?>
								<div class="slick-item article-item item-<?php echo $k+1;?>" style="width: 320px;">
									<div class="article-image block"><a href="<?php echo $url;?>"> <img src="<?php echo $attachment;?>" alt="Image"/></a></div>
									<div class="article-content-block">
										<h4 class="article-title block"><a href="<?php echo $url;?>"><?php echo $blog_item['article_title']?></a></h4>
										<div class="article-content"><?php echo $content;?></div>
									</div>
								</div>
								<?php } ?>
							</div>
							<!--
							<ul class="slide-nav block">
							<?php 
								$count = ceil( count($articles) / 3) ;
								for( $i = 1; $i <= $count; $i++){
									echo "<li class='slive-nav-item'>$i</li>";
								}
							?>
							</ul>
							-->
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(window).ready(function(){
	$('#article-slider .slides').slick({
		infinite: true,
		slidesToShow: 3,
		// slidesToScroll: 3,
		dots: true,
		responsive:[
			{
				breakpoint: 960,
				settings: {
					slidesToShow: 2,
				}
			},
			{
				breakpoint: 640,
				settings: {
					slidesToShow: 1,
				}
			},
		]
	});
});
</script>
