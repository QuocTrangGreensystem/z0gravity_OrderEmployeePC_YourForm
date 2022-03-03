<?php
/* Input
 * @param: 'projectGlobalView' = Array(
		[ProjectGlobalView] => Array
			(
				[id] => 781
				[attachment] => spring-tree-flowers-meadow-60006.jpeg
				[is_file] => 1
				[is_https] => 1
			)

		)
	
 * @param: 'projectImage' = Array(
		[0] => Array
			(
				[ProjectImage] => Array
					(
						[id] => 358
						[project_id] => 2090
						[file] => zac_aix.jpg
						[size] => 384891
						[type] => image
						[created] => 2017-01-10 10:48:14
						[updated] => 2017-01-10 10:48:14
						[company_id] => 32
						[is_file] => 1
					)

			)

		[1] => Array ( ... )
	
*/
// ob_clean(); debug($projectGlobalView); 
 // debug($projectImage); exit;
if ( empty($projectGlobalView) && empty($projectImage) ) return;
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->script('slick.min'); 

echo $html->script('jquery.fancybox.pack');
// echo $html->css('jquery.ui.custom');
echo $html->css(array('multipleUpload/jquery.plupload.queue', 'jquery.fancybox'));

$projectGlobalView = $projectGlobalView['ProjectGlobalView'];  
$widget_title = !empty( $widget_title) ? $widget_title : __('Project Image', true);

?>
<div class="wd-widget project-images-widget">
	<div class="wd-widget-inner">
		<div class="widget-title wd-hide">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
		</div>
		<div class="widget_content">
			<div id="vision-images-slider" class="wd-slick-slider">
				<div class="slides">
					<?php if( isset($projectGlobalView['is_file']) && $projectGlobalView['is_file']  && isset($projectGlobalView['is_https']) && $projectGlobalView['is_https']){ ?>
						<div class="wd-slider-item">
							<div class="image-item project-global-view">
								<?php 
								$link = $this->Html->url(array('controller' => 'project_global_views_preview', 'action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
								?>
								<img src="<?php echo $link; ?>"></img>
                                <a href="<?php echo $link; ?>" class="fancy image-expand-btn" rel="vision_images_slider" data-fancybox="image" data-type="image" tabindex="0"></a>
							</div>
							
						</div>
					<?php } ?> 
					<?php if( !empty( $projectImage ) ) { 
						foreach ($projectImage as $image) {
							if( $image['ProjectImage']['type'] != 'image' )
								continue;
							$link = $this->Html->url(array('controller' => 'project_images_preview', 'action' => 'attachment', $projectName['Project']['id'], $image['ProjectImage']['id'], '?' => array('sid' => $api_key)), true);
							?>
							<div class="wd-slider-item">
								<div class="image-item project-image">
									<div class="image-present">
										<img data-id="<?php echo $image['ProjectImage']['id'] ?>" src="<?php echo $link; ?>" alt="">
										<a href="<?php echo $link ?>" class="fancy image-expand-btn" rel="vision_images_slider" class="btn btn-frame"></a>
									</div>
								</div>
							</div>
						<?php }
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>	

<script>

	function vision_slider(){
        var _slider = $('#vision-images-slider .slides');
        var active_index = 0, index=0;

        if( _slider.length == 0) return;
        var item = _slider.children('.wd-slider-item').length;
        if( item <= 1) return;
		
        var slick_slider = _slider.each(function(){
			$(this).slick({
				infinite: true,
				slidesToShow: 1,
				//slidesToScroll: slider_show,
				speed: 600,
				arrows: true,
				dots: false,
				//centerMode: true,
				focusOnSelect: true,
				centerPadding: '0',
				prevArrow: '<button type="button" class="slick-prev"><i class="icon-arrow-left"></i></button>',
				nextArrow: '<button type="button" class="slick-next"><i class="icon-arrow-right"></i></button>',
			
				
			});
		});
		
    }
    vision_slider();
	$("a.fancy").fancybox({
        type: 'image',
    });
</script>

<style>
	.wd-widget.project-images-widget .widget_content{
		padding: 10px;
	}
	#vision-images-slider{
		position: relative;
	}
	#vision-images-slider *{
		box-sizing: border-box;
	}
	#vision-images-slider .wd-slider-item img{
		max-width: 100%;
		height: auto;
		margin: auto;
	}
	#vision-images-slider .slick-track{
		
	}
	
	#vision-images-slider .slick-arrow{
        left: 20px;
		top: 50%;
		margin-top: -30px;
        font-size: 40px;
        color: #fff;
        width: 60px;
        height: 60px;
        padding: 10px;
        line-height: 40px;
        text-align: center;
        z-index: 1;
        transition: all 0.4s ease ;
    }
    #vision-images-slider .slick-arrow:before{
        display: none;
    }
    #vision-images-slider .slick-arrow.slick-next{
        left: inherit;
        right: 20px;
    }
    #vision-images-slider:hover .slick-arrow{
        background-color: rgba(84, 135, 255, 0.5);
    }
    @media(max-width: 1199px){
        #vision-images-slider{
            width: 100%;
        }
        #vision-images-slider .slick-arrow{
            background-color: rgba(84, 135, 255, 0.5);
        }
        #carousel{
            position: relative;
            width: 100%;
            padding: 0;
            margin: 0px;
            margin-top: 40px
        }

    }
	
	#vision-images-slider .image-item{
		position: relative;
		
	}
	#vision-images-slider .image-expand-btn {
		position: absolute;
		top: 10px;
		right: 10px;
		width: 40px;
		height: 40px;
		border-radius: 50%;
		box-shadow: 0 0 10px 0px rgba(29,29,27,0.2);
		text-align: center;
		background: rgba( 255,255,255,0.4) url('/img/new-icon/expand_black.png') center no-repeat;
	}
	#vision-images-slider .wd-slider-item{
		max-height: 300px;
		overflow: hidden;
	}
		
	}
</style>