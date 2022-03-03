<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility(); 
echo $this->Html->css(array(
	'preview/layout',
	'layout_2019',
	'project_article.css?ver=1.1',
	'dropzone.min',
	'preview/datepicker-new',
	'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap',
));
echo $html->script(array(
	'dropzone.min',
	'jscolor',
	'tinymce/tinymce.min',
));
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content clearfix">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="edit-article-container article-container">
						<div class="edit-article-inner">
<?php 
$article_id = !empty( $article['ProjectCommunication']['id']) ? $article['ProjectCommunication']['id'] : '';
$company_id = !empty( $employee_info['Company']['id']) ? $employee_info['Company']['id'] : '';
echo $this->Form->create('ProjectCommunication', array(
	'type' => 'POST',
	'url' => array(
		'controller' => $this->params['controller'],
		'action' => 'update',
		$project_id
	),
	'class' => 'form-style-2019',
	'id' => 'ProjectCommunicationEditForm',
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
	'public_info' => __('Published on %1$s, by %2$s', true),
	'dropz_image' => '<p class="dzmsg-title">'.__('Drag & Drop your image.', true).'</p><p class="dzmsg-subtitle">'. __('or browse to choose a file', true) .'</p><p class="dzmsg-recommend">('. sprintf( __('%1$sx%2$s pixels recommended', true), 1920, 1080) .')</p>',
);
for ($m=1; $m<=12; $m++) {
	$month = date('M', mktime(0,0,0,$m, 1, 2000));
	$i18ns[$m] = __($month,true);
	$i18ns[$month] = __($month,true);
}
if( !empty($this->data['ProjectCommunication']['id'])){
	$link = $this->Html->url(array(
		'controller' => $this->params['controller'], 
		'action' => 'view',
		$this->data['ProjectCommunication']['public_key']
	));
}else{
	$link = $this->Html->url(array(
		'controller' => $this->params['controller'], 
		'action' => 'index',
		$company_public_key
	));
}
$link = urlencode($link);
$socials = array(
	'mail' => array(
		'url' => 'mailto:?subject='.$projectName['Project']['project_name'].'&body='.$link,
		'text' => __('Send mail', true),
		// 'icon' => '<i class="icon-envelope"></i>'
		'icon' => $icons['email']
	),
	'facebook' => array(
		'url' => 'https://www.facebook.com/sharer.php?u='.$link,
		'text' => __('Share on Facebook', true),
		// 'icon' => '<i class="icon-social-facebook"></i>'
		'icon' => $icons['facebook'],
	),
	'twitter' => array(
		'url' => 'https://twitter.com/intent/tweet?url='.$link,
		'text' => __('Share on Twitter', true),
		// 'icon' => '<i class="icon-social-twitter"></i>'
		'icon' => $icons['twitter'],
	),
	'linkedin' => array(
		'url' => 'https://www.linkedin.com/cws/share?url='.$link,
		'text' => __('Share on Linkedin', true),
		// 'icon' => '<i class="icon-social-linkedin"></i>',
		'icon' => $icons['linkedin'],
	),
);
// debug( $this->data);
?>
 					
<div class="wd-row">
	<div class="wd-col wd-col-lg-8"> 
		<div class="list-actions wd-title">
			<a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable(this);" title="<?php __('Show an overview of the communication this project');?>"></a>
			<a href="javascript:void(0);" class="btn btn-collapse wd-hide" id="table-collapse" onclick="collapse_table(this);" title="<?php __('Collapse');?>" style="display: none;"></a>
			<a href="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index', $company_public_key));?>" target="_blank" class="btn btn-table-view" id="btn-view-article" title="<?php __('View an overview of the communication of published projects');?>"><i class="icon-eye"></i></a>
			<a href="javascript:void(0);" class="btn btn-table-admin toggle-view-admin" id="btn-view-admin" title="<?php __('Admin');?>"><i class="icon-settings"></i></a>
			
		</div>
		<div class="left-block-scroll">
			<div class="wd-blog-info block custom-background-color">
				<div class="text-left">
					<a href="https://z0gravity.com" target="_blank" title="z0gravity.com" class="logo"> <?php echo $icons['logo'];?></a>
					<span class="public-info">
						<?php 
							if( !empty($this->data['ProjectCommunication']['public_date'])){
							$public_date = strtotime( $this->data['ProjectCommunication']['public_date'] );
							$public_date = date('d', $public_date) . ' ' . __(date('M', $public_date), true) . ' ' . date('Y',$public_date);
							$publisher = !empty($this->data['ProjectCommunication']['publisher']) ? $this->data['ProjectCommunication']['publisher'] : $this->data['Employee']['fullname'];
							echo sprintf( __('Published on %1$s, by %2$s', true) , $public_date , $publisher );
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
			<!-- 
			<h2 class="wd-project-title block"><?php echo $projectName['Project']['project_name'];?></h2>
			-->
			<?php 
			echo $this->Form->input('communication_title', array(
					'div' => array(
						'class' => 'wd-blog-title wd-input no-border block'
					),
					'required' => true,
					'default' => $projectName['Project']['project_name'],
					'placeholder' => __('Article title',true), 
					'label' => false,
				));
				?>
			<?php
			echo $this->Form->input('remove_image', array(
				'div' => array(
					'class' => 'wd-input hidden'
				),
				'type' => 'hidden',
				'default' => '0',
			));
			$attachment_class = 'has_attachment';
			$attachment_sty = '';
			$dz_sty = 'style="display: none;"';
			$has_attachment = !empty($this->data['ProjectCommunication']['image']);
			if( !$has_attachment){
				$attachment_class = '';
				$attachment_sty = 'style="display: none;"';
				$dz_sty = '';		
			}
			?>
			<div class="article_attachment block40 <?php echo $attachment_class;?>" <?php echo $attachment_sty;?>>
				<?php 
				$attachment_url = $this->Html->url(array(
					'controller' => $this->params['controller'],
					'action' => 'attachment',
					$project_id,
					$this->data['ProjectCommunication']['id'],
					));
				?>
				<img src="<?php echo $attachment_url;?>" alt="<?php echo $this->data['ProjectCommunication']['image'];?>">
				<a href="javascript:void(0);" id="article_attachment-action" class="article_attachment-action btn btn-collapse hide-on-preview"></a>
			</div>
			<div id="project_article_template_attach" class="project_article_template_attach block40" <?php echo $dz_sty;?>>
				<div class="heading">
				</div> 
				<div class="trigger-upload-fullwidth">
					<div id="upload-preview" method="post" action="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'update', $project_id)); ?>" class="dropzone custom-border-color" value="">
						<div class="fallback">
							<input name="file" type="file" />
						</div>
					</div>
				</div>
			</div>
			<div class="date-status-group block40 article-content-info">
				<div class="wd-block-date">
					<?php  echo $this->Form->input('start_date', array(
						'type'=> 'text',
						'label' => __('Start date', true),
						'class' => 'wd-date',
						'autocomplete' => 'off',
						'rel' => 'no-history',
						'div' => array(
							'class' => 'wd-input wd-block-round has-val'
						)
					));	?>	
				</div>
				<div class="wd-block-date">
					<?php echo $this->Form->input('end_date', array(
						'type'=> 'text',
						'label' => __('End date', true),
						'class' => 'wd-date',
						'autocomplete' => 'off',
						'rel' => 'no-history',
						'div' => array(
							'class' => 'wd-input wd-block-round has-val'
						)
					));
					?>
				</div>
				<div class="status-group hide-on-preview">
					<div class="wd-group-input wd-block-round">
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
				<div class="wd-text-block status-text-group show-on-preview-only">
					<span class="wd-text-block-label"><?php echo __('Project status', true);?></span>
					<?php 
					$stt = !empty( $this->data['ProjectCommunication']['status']);
					?>
					<span class="wd-text-block-content">
						<span class="status status0<?php echo $stt ? ' wd-hide' : '';?>"><?php echo __('Closed', true);?></span>
						<span class="status status1<?php echo $stt ? '' : ' wd-hide';?>"><?php echo __('In Progress', true);?></span>
						<span class="status-text">  </span>
					</span>
				</div>
			</div>
			<div class="article-content block40">
				<?php 
				echo $this->Form->input('content', array(
					'type' => 'textarea',
					'label' => false,
					'div' => array(
						'class' => 'wd-input wd-input-tinymce hide-on-preview',
					),
				));
				?>
				<div class="content-preview show-on-preview-only">
					<?php if( !empty( $this->data['ProjectCommunication']['content'])){
						echo $this->data['ProjectCommunication']['content'];
					}
					?>
				</div>
			</div>
			<div class="article-ex-url block">
				<?php 
				$class_empty = (isset($this->data['ProjectCommunication']['sub_title']) && $this->data['ProjectCommunication']['sub_title'] != '') ? '' : ' empty';
				echo $this->Form->input('sub_title', array(
					'div' => array(
						'class' => 'wd-blog-subtitle wd-input no-border block' .$class_empty
					),
					'required' => false,
					'default' => __('Become a key player in your sector:', true),
					'placeholder' => ' ', //__('Become a key player in your sector:',true), 
					'label' => false,
				));
				?>
				<ul class="article-urls" id="article-urls">
				<?php
				$k = 0;
				if( !empty($article['ProjectCommunicationUrl'])){
					foreach($article['ProjectCommunicationUrl'] as $k=>$url ){?>
						<li class="article-url" data-index="<?php echo $k;?>">
							<div class="icon custom-color">
								<i class="icon-link"></i>
								
							</div>
							<div class="url-content">
								<?php
								echo $this->Form->input('ProjectCommunicationUrl.'.$k.'.id', array(
									'type' => 'hidden',
									'label' => false,
									'div' => false
								));
								echo $this->Form->input('ProjectCommunicationUrl.'.$k.'.descriptions', array(
									'type' => 'text',
									'label' => false,
									'div' => array(
										'class' => 'wd-input url-title no-border',
									),
								));
								echo $this->Form->input('ProjectCommunicationUrl.'.$k.'.url', array(
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
				<a href="javascript:void(0);" class="new-article-url-button custom-hover-color hide-on-preview">
					<span class="add_button"></span>
					<?php echo __('Add an external link', true);?>
				</a>
			</div>
		</div>
	</div>
	<!-- <div class="wd-col wd-col-lg-1 wd-col-visible-lg wd-hide">
		
	</div> -->
	<div class="wd-col wd-col-lg-4">
		<div class="wd-article-preview wd-list-article wd-hide show-on-preview-only">
			<div class="slick-item article-item">
				<div class="article-image block">
					<a href="javascript:void(0);" tabindex="0"> <img class="img-preview" src="<?php 
						echo $this->Html->url(array(
							'controller' => $this->params['controller'],
							'action' => 'attachment',
							$project_id,
							( !empty($this->data['ProjectCommunication']['id']) ? $this->data['ProjectCommunication']['id'] : 0),
							'thumb'
							));
						?>" alt="Image">
					</a>
				</div>
				<div class="article-content-block">
					<h4 class="article-title block custom-hover-color"><a href="javascript:void(0);" class="custom-hover-color">
					</a></h4>
					<div class="article-content"></div>
				</div>
			</div>
		</div>
		<div class="wd-article-preview-hide hide-on-preview">
			<div class="setting-block setting-toggle">
				<p class="block-title block60">
					<?php echo __('Information to publish your project', true);?>
				</p>
			</div>
			<div class="setting-block setting-toggle wd-hide back-button-cont">
				<a href="javascript:void(0)" class="toggle-view-admin block back-button custom-hover-color"><i class="icon-arrow-left"></i><span><?php echo __('Back');?></span></a>
			</div>
			<div class="setting-block setting-toggle">			
				<div class="wd-row space5">
					<?php 
						echo $this->Form->input('Project.project_name', array(
							'div' => array(
								'class' => 'wd-input has-val'
							),
							'disabled' => true,
							'default' => $projectName['Project']['project_name'],
							'label' => __('Project name', true),
						));
					?>
					<div class="wd-col wd-col-md-6 public-date"> 
						<?php 
						echo $this->Form->input('ProjectCommunication.public_date', array(
							'type' => 'text',
							'label' => __('Public date', true),
							'default' => date('d-m-Y', time()),
							'class' => 'wd-date',
							'autocomplete' => 'off',
							'rel' => 'no-history',
							'div' => array(
								'class' => 'wd-input has-val'
							)
						));?>
					</div>
					<div class="wd-col wd-col-md-6 publisher"> 
						<?php echo $this->Form->input('ProjectCommunication.publisher', array(
							'type' => 'text',
							'label' => __('Editor', true),
							'default' => $employee_info['Employee']['fullname'],
							'onChange' => 'changeCustomColor(this)',
							'div' => array(
								'class' => 'wd-input has-val'
							),
						));
						?>
					</div>
				</div>
				
				<?php echo $this->Form->input('ProjectCommunication.published', array(
					'label' => __('Publication', true),
					'type' => 'checkbox',
					'class' => 'hidden',
					'required' => false,
					'before' => '<label class="label">' . __('Publication', true) .'</label>',
					'label' => '<span class="wd-btn-switch"><span></span></span><span class="checked">'. __('Yes', true) . '</span><span class="unchecked">'. __('No', true) . '</span>',
					'div' => array(
						'class' => 'wd-input wd-checkbox-switch wd-switch-status',
					),
				));
				?>
			
			</div>
			<div class="setting-block setting-toggle wd-hide">
			<?php 
			
				echo $this->Form->input('CompanyConfig.communication_title', array(
					'div' => array(
						'class' => 'wd-input wd-communication_title label-inline has-val'
					),
					// 'disabled' => ($employee_info['Role']['name'] != 'admin'),
					'required' => false,
					'default' => !empty($companyConfigs['communication_title']) ? $companyConfigs['communication_title'] : '',
					'label' => __('Title', true),
				));
				echo $this->Form->input('ProjectCommunication.custom_color', array(
					'type' => 'text',
					'label' => __('Color code', true),
					'class' => 'jscolor',
					'default' => '217FC2',
					'onChange' => 'changeCustomColor(this)',
					'div' => array(
						'class' => 'wd-input has-val article-custom-color-input label-inline has-val'
					),
				));
				$iframe_only = isset($companyConfigs['communication_iframe_only']) ? $companyConfigs['communication_iframe_only']!='0' : false;
				echo $this->Form->input('CompanyConfig.communication_iframe_only', array(
					'type' => 'checkbox',
					'label' => __('Only visible for Iframe', true),
					'checked' => $iframe_only,
					'div' => array(
						'class' => 'wd-input wd-checkbox-switch wd-switch-iframe',
					),
				));
				echo $this->Form->input('CompanyConfig.white_list_httpreferer', array(
					'type' => 'textarea',
					'label' => __('Authorized publication on the following sites', true),
					'default' => !empty($companyConfigs['white_list_httpreferer']) ? $companyConfigs['white_list_httpreferer'] : '',
					'div' => array(
						'class' => 'wd-input' . ( $iframe_only ? '' : ' wd-hide')
					),
				));
				echo $this->Form->input('embed', array(
					'type' => 'textarea',
					'label' => '<span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span><span class="wd-hide embed-copied custom-color">'.__('Copied', true).'</span>',
					'disabled' => true,
					'value' => '<iframe src="'. $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index', $company_public_key)) .'" width="1200" height="700" frameborder="0" allowfullscreen></iframe>',
					'div' => array(
						'class' => 'wd-input'
					),
				));
			?>
			</div>
		</div>
		<div class="wd-row wd-submit-row hide-on-preview">
			<div class="wd-col-xs-12">
				<div class="wd-submit">
					<button type="submit" class="btn-form-action btn-ok btn-right custom-background-color" id="btnSaveTask">
						<span><?php __('Save') ?></span>
					</button>
					<a class="btn-form-action btn-cancel custom-background-liner-color" id="reset_button" href="javascript: window.history.back();" onclick="cancel_popup(this);">
						<?php echo __("Cancel", true); ?></span>
					</a>
				</div>
			</div>
		</div>
					
	</div>
</div>
<?php echo $this->Form->end(); ?>
<ul class="new_ex_url_template hidden">
	<li class="article-url hide-on-preview" data-index="%index%">
		<div class="icon custom-color">
			<i class="icon-link"></i>
		</div>
		<div class="url-content">
			<?php
			echo $this->Form->input('ProjectCommunicationUrl.%index%.descriptions', array(
				'type' => 'text',
				'label' => false,
				'div' => array(
					'class' => 'wd-input url-title',
				),
			));
			echo $this->Form->input('ProjectCommunicationUrl.%index%.url', array(
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
<div id="dz-template-container" class="dz-template-container hidden">
	<div class="dz-preview dz-file-preview dz-file-fullwidth-preview">
		<img data-dz-thumbnail class="dz-thumbnail"/>
		<a href="javascript:void(0);" data-dz-remove class="btn btn-collapse dz-remove hide-on-preview"></a>
	</div>
</div>

						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<?php $c_color = $this->data['ProjectCommunication']['custom_color'];?>
<style id="edit-custom-color">
body .article-container .custom-color{
	color: #<?php echo $c_color;?>;
}
body .article-container .custom-background-color{
	background-color: #<?php echo $c_color;?>;
}
body .article-container .custom-background-liner-color{
	background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#<?php echo $c_color;?> 64%,#<?php echo $c_color;?> 100%);
}
body .article-container .custom-hover-color:hover{
	color: #<?php echo $this->data['ProjectCommunication']['custom_color'];?>;
}
body .article-container .custom-border-color{
	border-color: #<?php echo $this->data['ProjectCommunication']['custom_color'];?>;
}
</style>
<script>
var i18ns = <?php echo json_encode($i18ns); ?>;
var project_id = <?php echo json_encode($project_id); ?>;
var has_attachment = <?php echo json_encode($has_attachment); ?>;
var image_size = <?php echo json_encode($image_size); ?>;
var image_thumb = <?php echo json_encode($image_thumb); ?>;
var thumb_url = <?php echo json_encode(
		$this->Html->url(
			array(
				'controller' => $this->params['controller'],
				'action' => 'attachment',
				$project_id,
				$this->data['ProjectCommunication']['id'],
				'thumb'
			)
		)
	); 
?>;
var tinymce_options = {
	selector: '#ProjectCommunicationContent',
	menubar: false,
	skin: 'z0_Communication',
	image_advtab: false,
	language: Azuree.language,
	remove_script_host : true,
	convert_urls : true,
	relative_urls : false,
	plugins: 'autolink lists link anchor fullscreen table textcolor colorpicker fullscreen paste autoresize',
	toolbar:  'undo redo | bold italic forecolor styleselect | alignleft aligncenter alignright alignjustify | bullist numlist | link removeformat fullscreen',
	autoresize_min_height: 200,
	autoresize_bottom_margin: 0,
	setup: function (editor) {
		editor.on('change', function () {
			tinymce.triggerSave();
			$('.content-preview').html( $('#ProjectCommunicationContent').val());
		});
	},
};
$(window).ready(function(){
	var new_url_template = $('.new_ex_url_template').html();
	tinymce.init(tinymce_options);
	$('.new-article-url-button').on('click', function(){
		var new_index = $('.article-urls').children().length + 1;
		var new_url = new_url_template.replace(/%index%/g, new_index);
		$('.article-urls').append(new_url);
	});
	$('.toggle-view-admin').on('click', function(){
		$('.setting-toggle').toggleClass('wd-hide');
	});
	if( Clipboard.isSupported()){
		var copied_timeout=0;
		$('.copy-embed-code, #ProjectCommunicationEmbed').on('click', function(){
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val($('#ProjectCommunicationEmbed').val()).select();
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
	$("#ProjectCommunicationStartDate, #ProjectCommunicationEndDate").on('change', function(e){
		var _input = $(this);
		var date = _input.datepicker( "getDate" );
		if( date){
			if( _input.is($("#ProjectCommunicationStartDate"))){
				$("#ProjectCommunicationEndDate").datepicker( "option", "minDate", date);
			}else{
				$("#ProjectCommunicationStartDate").datepicker( "option", "maxDate", date);
			}
		}
	});
	$("#ProjectCommunicationStartDate, #ProjectCommunicationEndDate, #ProjectCommunicationPublicDate").datepicker({
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
	customstyle += 'body .article-container .custom-border-color{ border-color: #' + val + ';}';
	customstyle += 'body .article-container .custom-background-liner-color{	background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#' + val + ' 64%,#' + val + ' 100%);}';
	$('#edit-custom-color').html(customstyle);
}
Dropzone.autoDiscover = false;
// $(function() {
	var dropzone_elm = "#upload-preview";
	var articleForm = $(dropzone_elm).closest('form');
	var dz_elm = new Dropzone( dropzone_elm,{
		maxFiles: 1,
		autoProcessQueue: false,
		addRemoveLinks: true,
		acceptedFiles: "image/jpeg,image/png,image/gif",
		// previewsContainer: '.dropzone-previews',
		thumbnailWidth: image_size.w,
		thumbnailHeight: null,
		// thumbnailHeight: image_size.h,
		// resizeMethod: 'contain ',
		dictDefaultImg: '<img title="burger" src="/img/new-icon/dropzone-icon.png">',
		dictDefaultMessage: i18ns.dropz_image, 
		previewTemplate: $('#dz-template-container').html(),
	});
	dz_elm.on("removedfile", function(file) {
		$('.wd-article-preview').find('.article-image').addClass('loading-mark loading');
		setTimeout( function(){
			show_artivle_preview();
			$('.wd-article-preview').find('.article-image').removeClass('loading');
		
		}, 1000);
	});
	dz_elm.on("error", function(file, message, xhr) {
		dz_elm.removeFile(file);
	});
	
	dz_elm.on("addedfile", function(file) {
		// console.log( file);
		// if( !file.accepted) dz_elm.removeFile(file);
		$('.wd-article-preview').find('.article-image').addClass('loading-mark loading');
		setTimeout( function(){
			show_artivle_preview();
			$('.wd-article-preview').find('.article-image').removeClass('loading');
		
		}, 1000);
	});
	dz_elm.on("queuecomplete", function(file) {
		// location.href = '<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index', $project_id));?>';
		location.reload();
	});
	articleForm.on('submit', function(e){
		if(dz_elm.files.length){
			e.preventDefault();
			// console.log( 'dropzone submit');
			$('.article-container').addClass('loading-mark loading');
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
// });
$('#article_attachment-action').on('click', function(){
	$('.article_attachment').hide();
	$('#project_article_template_attach').show();
	$('#ProjectCommunicationRemoveImage').val("1");
});
$('#ProjectCommunicationStatus').on('change', function(){
	var val = $(this).is(':checked');
	var text_status = $('.article-content-info').find('.status-text-group');
	if( val){
		text_status.find('.status0').hide();
		text_status.find('.status1').show();
	}else{
		text_status.find('.status0').show();
		text_status.find('.status1').hide();
		
	}
});
$('#ProjectCommunicationStatusText').on('change', function(){
	$('.article-content-info').find('.status-text-group').find('.status-text').text($(this).val());
});
$('#ProjectCommunicationSubTitle').on('change', function(){
	if( $(this).val() =='') $(this).parent().addClass('empty');
	else $(this).parent().removeClass('empty');
});
$('#CompanyConfigCommunicationIframeOnly').on('change', function(){
	var _checked = $(this).is(':checked');
	var _target = $('#CompanyConfigWhiteListHttpreferer').closest('.wd-input');
	if( _checked) _target.removeClass('wd-hide');
	else  _target.addClass('wd-hide');
});
function show_artivle_preview(){
	var src = thumb_url;
	if( dz_elm.files.length){
		src = dz_elm.files[0]['dataURL'];
	}
	$('.wd-article-preview').find('.img-preview').attr('src', src);
	$('.wd-article-preview').find('.article-title a').text($('#ProjectCommunicationCommunicationTitle').val());
	var content = $('#ProjectCommunicationContent').val();
	var max_len = 160;
	content = wd_remove_tag(content);
	if( content.length > max_len){
		content = content.substring(0, max_len);
		content += '(...)';
	}
	$('.wd-article-preview').find('.article-content').html(content);
}
expandTable = function(elm){
	$(elm).closest('.wd-layout').addClass('fullScreen');
	$(elm).closest('.wd-title').find('.btn-collapse').css('display', ''); // show
	$(elm).closest('.wd-title').find('.btn-expand').css('display', 'none'); // hide
	/* Preview article*/
	show_artivle_preview();
	if( !dz_elm.files.length){
		$('.article_attachment').show();
		$('#project_article_template_attach').hide();
	}
	$('.left-block-scroll').find('input:not(hidden)').prop('disabled', true);
	/* END Preview article*/
	$(window).trigger('resize');
}

collapse_table = function(elm){
	$(elm).closest('.wd-layout').removeClass('fullScreen');
	$(elm).closest('.wd-title').find('.btn-collapse').css('display', 'none');
	$(elm).closest('.wd-title').find('.btn-expand').css('display', ''); 
	/* Off preview */
	if( !dz_elm.files || ($('#ProjectCommunicationRemoveImage').val() == '1') || (!$('.article_attachment').hasClass('has_attachment')) ){
		$('.article_attachment').hide();
		$('#project_article_template_attach').show();
	}else{
		
	}
	$('.left-block-scroll').find('input:not(hidden)').prop('disabled', false);
	/* Off preview */
	$(window).trigger('resize');
}
</script>