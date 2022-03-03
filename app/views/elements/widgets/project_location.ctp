<?php
/* Input
 * @param: 
	
*/
// ob_clean(); debug($projectMilestones); exit;
$gapi = GMapAPISetting::getGAPI();
// debug($projectName);
if ( empty($projectName['Project']['address']) && empty($gapi) ) return;
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->script('slick.min'); 

echo $html->script('jquery.fancybox.pack');
// echo $html->css(array('jquery.ui.custom', 'multipleUpload/jquery.plupload.queue', 'jquery.fancybox'));


$widget_title = !empty( $widget_title) ? $widget_title : __('Project Location', true);

?>
<div class="wd-widget wd-map-widget">
	<div class="wd-widget-inner">
		<div class="widget-title wd-hide">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
		</div>
		<div class="widget_content">
			<div class='wd-map-area wd-loading'>
			   <!--  <div class="fullscreen"><i class="icon-frame"></i></div> -->
				<a class="image-expand-btn" href="javascript:;" onclick="wd_map_expandScreen(this);"></i></a>
				<a class="image-collapse-btn" href="javascript:;" onclick="wd_map_collapseScreen(this);"></a>
				<iframe src="about:blank" style="height: 300px; display: none;" id="map-frame" ></iframe>
				<input type="hidden" id="coord-input" onfocusout="refreshMapYourForm(this)" name="data[Project][address]" size="40" >
				<?php 
				$linkImg = $this->Html->url(array('controller' => 'projects_preview','action' => 'attachment_static', '?' => array('sid' => $api_key)), true);
				?>
				<div class="imgprinter wd-hide" style="background: url('<?php echo $linkImg; ?>') center no-repeat;">
					<img id="img_location" style="width: auto; height: 300px; margin-top: 0px; opacity: 0;" src="<?php echo $linkImg; ?>">
				</div>
			</div>
		</div>
	</div>
</div>	

<script>
	var _map_widget = $('.wd-map-widget');
	function wd_map_expandScreen(_element){
		var _this = $(_element);
        // $('.wd-map-area').addClass('fullScreen');
		
        _map_widget.addClass('fullScreen');
		_map_widget.find('iframe').height(
			_map_widget.height() - parseInt(_map_widget.find('.widget_content').css('padding-top')) - parseInt( _map_widget.find('.widget_content').css('padding-bottom'))
		);
		_map_widget.closest('li').addClass('wd_on_top');
		
		_this.closest('.wd-col').css('width', '100%').siblings().hide();
		_this.closest('.wd-row').siblings().hide();
		$('#wd-container-header-main, .wd-indidator-header').hide();
		$('#layout').addClass('widget-expand');
    }
    function wd_map_collapseScreen(_element){
		var _this = $(_element);
        // $('.wd-map-area').removeClass('fullScreen');
        _map_widget.removeClass('fullScreen');
		_map_widget.find('iframe').height('300');
		_map_widget.closest('li').removeClass('wd_on_top');
		_this.closest('.wd-col').css('width', '').siblings().show();
		_this.closest('.wd-row').siblings().show();
		$('#wd-container-header-main, .wd-indidator-header').show();
		$('#layout').removeClass('widget-expand');
		$('#expand').show();
		$('#table-collapse').hide();
    }
	var gapi = <?php echo json_encode($gapi) ?>;
    function refreshMap(show){

        //var map_width = $('.wd-map-widget .wd-map-area').width();
        //$('.wd-map-area').find('iframe').css("width", map_width);
        $('.wd-map-area').find('iframe').css("width", '100%');

        var query = $.trim($('#coord-input').val());
        if( query ){
            //initial google maps
            $('#map-frame').prop('src', 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(query) + '&key=' + gapi);
            if( show ){
                $('#map-frame').show();
                $('#local-frame').hide();
                state = 0;
            }			
			_map_widget.find('.wd-map-area').removeClass('wd-loading');
        } else {
            $('#map-frame').prop('src', 'about:blank');
        }
    }
    function refreshMapYourForm($this){
        // var newAddress = $.trim($this.val());
        var field = $($this).attr('name');
        field = field.replace('data[Project][', '').replace(']', '');
        var value = $($this).val();
        console.log(field);
        refreshMap(true);
        $.ajax({
            url: '<?php echo $html->url(array('action' => 'saveFieldYourForm', $this->data['Project']['id'])); ?>',
            type: 'POST',
            data: {
                field : field,
                value : value
            },
        });
    }
	$(document).ready(function(){
        var saving = false;
		console.log('ready');
		setTimeout(function(){
			$('#coord-input').val(<?php echo json_encode($projectName['Project']['address']) ?>);
			// <?php if($projectName['Project']['address']): ?> refreshMap(true);  <?php endif ?>	
			<?php if($projectName['Project']['address']): ?> refreshMapYourForm('#coord-input');  <?php endif ?>	
		}, 3000);
		_map_widget.find('.wd-map-area').removeClass('wd-loading');
        
    });
</script>

<style>
	body #layout .wd_on_top{
		z-index: 200;
	}
	.wd-widget.wd-map-widget .widget_content{
		padding: 10px;
		border-radius: 4px;
		overflow: hidden;
	}
	.wd-map-widget .wd-map-area{
		min-height: 300px;
	}
	.wd-map-widget .wd-map-area,
	.wd-map-widget {
		border-radius: 4px;
		overflow: hidden;
	}
	.wd-map-widget iframe{
		width: 100%;
	}
	.image-collapse-btn,
	.image-expand-btn{
		position: absolute;
		top: 20px;
		right: 20px;
		width: 40px;
		height: 40px;
		border-radius: 50%;
		box-shadow: 0 0 10px 0px rgba(29,29,27,0.2);
		text-align: center;
		background: rgba( 255,255,255,0.4) url('/img/new-icon/expand_black.png') center no-repeat;
	}
	
	.image-collapse-btn:hover,
	.image-expand-btn:hover{
		background-color: rgba(84, 135, 255, 0.5);
	}
	.image-collapse-btn{
		display: none;
		background-image: url(/img/new-icon/close_black.png);
	}
	.fullScreen .image-collapse-btn{
		display: block;
	}
	
	.fullScreen .image-expand-btn{
		display: none;
	}
	.wd-loading{
		position: relative;
	}
	.wd-loading:after{
		content:'';
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: #fff url(/img/business/wait-1.gif) no-repeat center center;
		background-size: 30px;	
	}
	
</style>