<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility(); 
echo $this->Html->css(array(
	'layout',
	'preview/layout',
	'layout_2019',
	'project_article.css?ver=1.0',
	// 'dropzone.min',
	// 'preview/datepicker-new',
));
// echo $html->script(array(
	// 'dropzone.min',
	// 'jscolor',
// ));
if( empty($article['ProjectArticle']['custom_color'])) $article['ProjectArticle']['custom_color'] = '217FC2';
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="view-article-container article-container">
						<div class="view-article-inner">
<?php 
$icons = array(
	'logo' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 80 80">
		<g transform="translate(-56 -32)"><rect class="svg-a" width="80" height="80" transform="translate(56 32)"></rect><path class="svg-b" d="M147.612,20.638h-7.529a33.272,33.272,0,0,1,.17,3.335A32.5,32.5,0,0,1,86.5,48.558l-5.308,5.317a39.993,39.993,0,0,0,66.559-29.9C147.748,22.85,147.7,21.735,147.612,20.638Z" transform="translate(-11.748 48.027)"></path><path class="svg-c" d="M119.6,81.33a25,25,0,0,0,25-25,25.338,25.338,0,0,0-.221-3.335H119.6v6.67h17.184A17.5,17.5,0,1,1,130.2,42.4l5.334-5.342A25,25,0,0,0,94.829,52.993H87.275a32.5,32.5,0,0,1,53.586-21.251l5.308-5.317a39.993,39.993,0,0,0-66.559,29.9c0,1.123.043,2.237.136,3.335H94.829A25,25,0,0,0,119.6,81.33Z" transform="translate(-23.61 15.672)"></path></g>
		</svg>',
);
$i18ns = array(
	'public_info' => __('Published on %1$s, by %2$s', true)
);
for ($m=1; $m<=12; $m++) {
	$month = date('M', mktime(0,0,0,$m, 1, 2000));
	$i18ns[$m] = __($month,true);
	$i18ns[$month] = __($month,true);
}
	$link = $this->Html->url(array(
		'controller' => $this->params['controller'], 
		'action' => 'view',
		$article['ProjectArticle']['id']
	));
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
// debug( $article);
?>
<style id="article-custom-color">
	.article-container .custom-color{
		color: #<?php echo $article['ProjectArticle']['custom_color'];?>;
	}
	.article-container .custom-background-color{
		background-color: #<?php echo $article['ProjectArticle']['custom_color'];?>;
	}
	.article-container .custom-hover-color:hover{
		color: #<?php echo $article['ProjectArticle']['custom_color'];?>;
	}
</style>					
<div class="wd-article-container"> 
	<div class="wd-blog-info block custom-background-color">
		<div class="text-left">
			<span class="logo">
				<?php echo $icons['logo'];?>
			</span>
			<span class="public-info">
				<?php 
					if( !empty($article['ProjectArticle'])){
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
	<div class="wd-article-title block">
		<h2><?php echo $article['ProjectArticle']['article_title'];?></h2>
	</div>
	<div class="article_attachment block">
		<?php 
		$url = $this->Html->url(array(
			'controller' => $this->params['controller'],
			'action' => 'attachment',
			$project_id,
			$article['ProjectArticle']['id']
			));
		?>
		<img src="<?php echo $url;?>" title="<?php echo $article['ProjectArticle']['image'];?>">
	</div> 
	<div class="article-content-info block">
		<div class="wd-text-block wd-date start-date">
			<span class="wd-text-block-label">
				<?php echo __('Start date', true);?>
			</span>
			<span class="wd-text-block-content">
				<?php echo $article['ProjectArticle']['start_date'];?>
			</span>						
		</div>
		<div class="wd-text-block wd-date end-date">
			<span class="wd-text-block-label">
				<?php echo __('End date', true);?>
			</span>
			<span class="wd-text-block-content">
				<?php echo $article['ProjectArticle']['end_date'];?>
			</span>						
		</div>
		<div class="wd-text-block status-text-group">
			<span class="wd-text-block-label">
				<?php echo __('Project status', true);?>
			</span>
			<span class="wd-text-block-content">
				<span class="status status<?php echo $article['ProjectArticle']['status'];?>"><?php echo $article['ProjectArticle']['status'] ? __('In Progress', true) : __('Closed', true);?> </span>
				<span class="status-text"> <?php echo $article['ProjectArticle']['status_text'];?> </span>
			</span>
		</div>
	</div>
	<div class="article-content block">
		<?php echo $article['ProjectArticle']['content'];?>
	</div>
	<?php if( !empty($article['ProjectArticleUrl'])){ ?>
		<div class="article-ex-url block">
			<h2 class="wd-title ex-url-title block">
				<?php __('Become a key player in your sector:');?>
			</h2>
			<ul class="article-urls" id="article-urls">
			<?php foreach($article['ProjectArticleUrl'] as $k=>$url ){?>
				<li class="article-url" data-index="<?php echo $k;?>">
					<div class="icon custom-color">
						<i class="icon-link"></i>
						
					</div>
					<div class="url-content">
						<p class="url-title"><?php echo $url['descriptions'];?></p>
						<a href="<?php echo $url['url'];?>" target="_blank" class="custom-color">
							<?php echo $url['url'];?>
						</a>
					</div>
				</li>
			<?php } ?>
		</div>
	<?php }?>
</div>

						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<script>
expandTable = function(elm){
	$(elm).closest('.wd-layout').addClass('fullScreen');
	$(elm).closest('.wd-title').find('.btn-collapse').css('display', ''); // show
	$(elm).closest('.wd-title').find('.btn-expand').css('display', 'none'); // hide
	$(window).trigger('resize');
}

collapse_table = function(elm){
	$(elm).closest('.wd-layout').removeClass('fullScreen');
	$(elm).closest('.wd-title').find('.btn-collapse').css('display', 'none');
	$(elm).closest('.wd-title').find('.btn-expand').css('display', ''); 
	$(window).trigger('resize');
}
</script>