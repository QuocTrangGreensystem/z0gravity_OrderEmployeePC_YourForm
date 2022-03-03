<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility(); 
echo $this->Html->css(array(
	'layout',
	'preview/layout',
	'layout_2019',
	'project_article.css?ver=1.0',
	'dropzone.min',
	'preview/datepicker-new',
));
echo $html->script(array(
	'dropzone.min',
	'jscolor',
));
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="edit-article-container article-container">
						<div class="edit-article-inner">
<?php 
$article_id = !empty( $article['ProjectArticle']['id']) ? $article['ProjectArticle']['id'] : '';
echo $this->Form->create('ProjectArticle', array(
	'type' => 'POST',
	'url' => array(
		'controller' => $this->params['controller'],
		'action' => 'update'
	),
	'class' => 'form-style-2019',
	'id' => 'ProjectArticleEditForm',
	)
);
echo $this->Form->input('project_id', array(
	'type'=> 'hidden',	
	'value' => $project_id	
));
echo $this->Form->input('id', array(
	'type'=> 'hidden',					
));
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
if( !empty($this->data['ProjectArticle']['id'])){
	$link = $this->Html->url(array(
		'controller' => $this->params['controller'], 
		'action' => 'view',
		$this->data['ProjectArticle']['id']
	));
}else{
	$link = $this->Html->url(array(
		'controller' => $this->params['controller'], 
		'action' => 'communication',
		$project_id
	));
}
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
// debug( $this->data);
?>
 					
<div class="wd-row">
	<div class="wd-col wd-col-lg-8"> 
		<div class="list-actions wd-title">
			<a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable(this);" title="<?php __('Expand');?>"></a>
			<a href="javascript:void(0);" class="btn btn-collapse wd-hide" id="table-collapse" onclick="collapse_table(this);" title="<?php __('Collapse table');?>" style="display: none;"></a>
			<?php if( !empty($this->data['ProjectArticle']['id'])){ ?>
				<a href="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'view', $article_id));?>" target="_blank" class="btn btn-table-view" id="btn-view-article" title="<?php __('Preview');?>"><i class="icon-eye"></i></a>
			<?php } ?> 
			<a href="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index', $project_id));?>" class="btn btn-table-admin" id="btn-view-admin" title="<?php __('Admin');?>"><i class="icon-settings"></i></a>
			
		</div>
		<div class="left-block-scroll">
			<div class="wd-blog-info block custom-background-color">
				<div class="text-left">
					<span class="logo">
						<?php echo $icons['logo'];?>
					</span>
					<span class="public-info">
						<?php 
							if( !empty($this->data['ProjectArticle'])){
							$public_date = strtotime( $this->data['ProjectArticle']['public_date'] );
							$public_date = date('d', $public_date) . ' ' . __(date('M', $public_date), true) . ' ' . date('Y',$public_date);
							echo sprintf( __('Published on %1$s, by %2$s', true) , $public_date , $this->data['Employee']['fullname']);
						}?>
					</span>
				</div>				<div class="text-right">
					<div class="social-share">
						<ul>
						<?php
						foreach($socials as $social){
							$url = $social['url'];
							$icon = $social['icon'];
							$text = $social['text'];
							echo "<li class='social'><a href='{$url}' target='_blank' title='{$text}'>{$icon}</a></li>";
						} 
						?> 						</ul>
					</div>
				</div>
			</div>
			<?php 
				echo $this->Form->input('article_title', array(
					'div' => array(
						'class' => 'wd-blog-title wd-input no-border block'
					),
					'required' => true,
					'required' => true,
					'placeholder' => __('Article title',true), 
					'label' => false,
				));
				if( !empty($this->data['ProjectArticle']['image'])){?>
					<div class="article_attachment block">
						<?php 
						$url = $this->Html->url(array(
							'controller' => $this->params['controller'],
							'action' => 'attachment',
							$project_id,
							$this->data['ProjectArticle']['id']
							));
						?>
						<img src="<?php echo $url;?>" title="<?php echo $this->data['ProjectArticle']['image'];?>">
					</div> 
			<?php }?>
			
			<div id="project_article_template_attach block" >
				<div class="heading">
				</div> 
				<div class="trigger-upload">
					<div id="upload-preview" method="post" action="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'update')); ?>" class="dropzone" value="">
						<div class="fallback">
							<input name="file" type="file" />
						</div>
					</div>
				</div>
			</div>
			<div class="date-status-group block">
				<div class="date-group">
					<div class="date-group-inner">
						<?php 				
						echo $this->Form->input('start_date', array(
							'type'=> 'text',
							'label' => __('Start date', true),
							'required' => true,
							'class' => 'wd-date',
							'autocomplete' => 'off',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required has-val'
							)
						));			
						echo $this->Form->input('end_date', array(
							'type'=> 'text',
							'label' => __('End date', true),
							'required' => true,
							'class' => 'wd-date',
							'autocomplete' => 'off',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input label-inline required has-val'
							)
						));
					?>
					</div>
				</div>
				<div class="status-group">
					<div class="status-group-inner">
						<div class="wd-group-input">
							<?php
							echo $this->Form->input('status', array(
								'type' => 'checkbox',
								'before' => '<span class="label">' . __('Project status', true) .'</span>',
								'class' => 'hidden',
								'label' => '<span class="wd-btn-switch"><span></span></span><span class="checked">'. __('In Progress', true) . '</span><span class="unchecked">'. __('Closed', true) . '</span>',
								'div' => array(
									'class' => 'wd-input wd-checkbox-switch wd-switch-status',
								),
							));
							echo $this->Form->input('status_text', array(
								'type' => 'text',
								'label' => false,
								'placeholder' => __('Status comment',true), 
								'div' => array(
									'class' => 'wd-input wd-status-text',
								),
							));
							
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="article-content block">
				<?php 
				echo $this->Form->input('content', array(
					'type' => 'textarea',
					'label' => false,
					'div' => array(
						'class' => 'wd-input',
					),
				));
				?>
			</div>
			<div class="article-ex-url block">
				<h2 class="wd-title ex-url-title block">
					<?php __('Become a key player in your sector:');?>
				</h2>
				<ul class="article-urls" id="article-urls">
				<?php
				$k = 0;
				if( !empty($article['ProjectArticleUrl'])){
					foreach($article['ProjectArticleUrl'] as $k=>$url ){?>
						<li class="article-url" data-index="<?php echo $k;?>">
							<div class="icon custom-color">
								<i class="icon-link"></i>
								
							</div>
							<div class="url-content">
								<?php
								echo $this->Form->input('ProjectArticleUrl.'.$k.'.id', array(
									'type' => 'hidden',
									'label' => false,
									'div' => false
								));
								echo $this->Form->input('ProjectArticleUrl.'.$k.'.descriptions', array(
									'type' => 'text',
									'label' => false,
									'div' => array(
										'class' => 'wd-input url-title no-border',
									),
								));
								echo $this->Form->input('ProjectArticleUrl.'.$k.'.url', array(
									'type' => 'text',
									'label' => false,
									'class' => 'custom-color',
									'div' => array(
										'class' => 'wd-input url-link no-border custom-color',
									),
								));
								?>
							</div>
						</li>
						
					<?php } ?>
				<?php }
				$k++;
				?>
				
				</ul>
					<a href="javascript:void(0);" class="new-article-url-button custom-hover-color">
						<span class="add_button"></span>
						<?php echo __('Add an external link', true);?>
					</a>
				
			</div>
		</div>
	</div>
	<!-- <div class="wd-col wd-col-lg-1 wd-col-visible-lg wd-hide">
		
	</div> -->
	<div class="wd-col wd-col-lg-4">
		<?php 
		echo $this->Form->input('Project.project_name', array(
			'type' => 'text',
			'label' => __('Project name', true),
			'disabled' => true,
			'div' => array(
				'class' => 'wd-input label-inline has-val'
			),
		));
		echo $this->Form->input('ProjectArticle.custom_color', array(
			'type' => 'text',
			'label' => __('Color code', true),
			'class' => 'jscolor',
			'default' => '217FC2',
			'onChange' => 'changeCustomColor(this)',
			'div' => array(
				'class' => 'wd-input label-inline has-val article-custom-color-input'
			),
		));
		if( $article_id){
			echo $this->Form->input('embed', array(
				'type' => 'textarea',
				'label' => __('Embed code', true) . '<span class="copy-embed-code icon-docs custom-color" title="'.__('Click to copy embed code', true).'"></span><span class="wd-hide embed-copied custom-color">'.__('Copied', true).'</span>',
				'disabled' => true,
				
				'value' => '<iframe src="'. $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'view', $article_id)) .'" width="1280" height="540" frameborder="0" allowfullscreen></iframe>',
				'div' => array(
					'class' => 'wd-input'
				),
			));
		}
		?>
		<div class="wd-row wd-submit-row">
			<div class="wd-col-xs-12">
				<div class="wd-submit">
					<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSaveTask">
						<span><?php __('Save') ?></span>
					</button>
					<a class="btn-form-action btn-cancel" id="reset_button" href="javascript: window.history.back();" onclick="cancel_popup(this);">
						<?php echo __("Cancel", true); ?></span>
					</a>
				</div>
			</div>
		</div>
					
	</div>
</div>
<?php echo $this->Form->end(); ?>
<ul class="new_ex_url_template hidden">
	<li class="article-url " data-index="%index%">
		<div class="icon custom-color">
			<i class="icon-link"></i>
		</div>
		<div class="url-content">
			<?php
			echo $this->Form->input('ProjectArticleUrl.%index%.descriptions', array(
				'type' => 'text',
				'label' => false,
				'div' => array(
					'class' => 'wd-input url-title',
				),
			));
			echo $this->Form->input('ProjectArticleUrl.%index%.url', array(
				'type' => 'text',
				'label' => false,
				'class' => 'custom-color',
				'div' => array(
					'class' => 'wd-input url-link',
				),
			));
			?>
		</div>
	</li>
</ul>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<style id="edit-custom-color">
.article-container .custom-color{
	color: #<?php echo $this->data['ProjectArticle']['custom_color'];?>;
}
.article-container .custom-background-color{
	background-color: #<?php echo $this->data['ProjectArticle']['custom_color'];?>;
}
.article-container .custom-hover-color:hover{
	color: #<?php echo $this->data['ProjectArticle']['custom_color'];?>;
}
</style>
<script>
var i18ns = <?php echo json_encode($i18ns); ?>;
var project_id = <?php echo json_encode($project_id); ?>;
$(window).ready(function(){
	var new_url_template = $('.new_ex_url_template').html();
	$('.new-article-url-button').on('click', function(){
		var new_index = $('.article-urls').children().length + 1;
		var new_url = new_url_template.replace(/%index%/g, new_index);
		$('.article-urls').append(new_url_template);
	});
	if( Clipboard.isSupported()){
		var copied_timeout=0;
		$('.copy-embed-code, #ProjectArticleEmbed').on('click', function(){
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val($('#ProjectArticleEmbed').val()).select();
			$temp[0].setSelectionRange(0, 99999);
			document.execCommand("copy"); /*For mobile devices*/
			$temp.remove();
			$('.embed-copied').show();
			clearTimeout(copied_timeout);
			copied_timeout = setTimeout(function(){
				$('.embed-copied').fadeOut();
			}, 3000)
		});
	}else{
		$('.copy-embed-code').hide();
	}
	function update_scroll_height(){
		wdTable = $('.left-block-scroll');
		if( $(window).width() > 1200){
			var heightTable = $(window).height() - wdTable.offset().top - 80;
			wdTable.height(heightTable);
		}else{
			wdTable.css('height', '');
		}
	}
	$(window).resize(function(){
		update_scroll_height();
	});
	update_scroll_height();
	$("#ProjectArticleStartDate, #ProjectArticleEndDate").on('change', function(e){
		var _input = $(this);
		var date = _input.datepicker( "getDate" );
		if( date){
			if( _input.is($("#ProjectArticleStartDate"))){
				$("#ProjectArticleEndDate").datepicker( "option", "minDate", date);
			}else{
				$("#ProjectArticleStartDate").datepicker( "option", "maxDate", date);
			}
		}
	});
	$("#ProjectArticleStartDate, #ProjectArticleEndDate").datepicker({
		dateFormat      : 'dd-mm-yy',
		onSelect		: function(text, inst){
			var _input = $(this);
			_input.trigger('change');
		}
	});
});
function changeCustomColor(elm){
	var _this = $(elm);
	var val = _this.val();
	var customstyle = '';
	customstyle += 'body .article-container .custom-color{ color: #' + val + ';}';
	customstyle += 'body .article-container .custom-background-color{ background-color: #' + val + ';}';
	customstyle += 'body .article-container .custom-hover-color:hover{ color: #' + val + ';}';
	$('#edit-custom-color').html(customstyle);
}
Dropzone.autoDiscover = false;
$(function() {
	var dropzone_elm = "#upload-preview";
	var articleForm = $(dropzone_elm).closest('form');
	var dz_elm = new Dropzone( dropzone_elm,{
		maxFiles: 1,
		autoProcessQueue: false,
		addRemoveLinks: true,
		acceptedFiles: "image/jpeg,image/png,image/gif"
	});
	dz_elm.on("queuecomplete", function(file) {
		location.href = '<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index', $project_id));?>';
	});
	articleForm.on('submit', function(e){
		if(dz_elm.files.length){
			e.preventDefault();
			console.log( 'dropzone submit');
			dz_elm.processQueue();
		}
	});
	dz_elm.on('sending', function(file, xhr, formData) {
		// Append all form inputs to the formData Dropzone will POST
		var data = articleForm.serializeArray();
		$.each(data, function(key, el) {
			formData.append(el.name, el.value);
		});
	});
});
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