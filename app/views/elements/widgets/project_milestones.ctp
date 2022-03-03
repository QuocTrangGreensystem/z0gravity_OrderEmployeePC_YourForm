<?php
/* Input
 * @param: projectMilestones[891] {
	[id] => 891
    [project_milestone] => Attribution du marché
    [milestone_date] => 2014-12-12
    [validated] => 1
    [part_id] => 0
    [weight] => 9
	} 
	Sorted by date
	
*/
// ob_clean(); debug($projectMilestones); exit;
if ( empty($projectMilestones) ) return;
echo $html->css('slick'); 
echo $html->css('slick-theme');  
echo $html->script('slick.min');
$projectMilestones = Set::sort($projectMilestones, '{n}.milestone_date', 'asc');
$company_id = $projectName['Project']['company_id'];
$titleMenu = ClassRegistry::init('Menu')->find('first', array(
	'recursive' => -1,
	'conditions' => array(
		'company_id' => $company_id,
		'model' => 'project',
		'controllers' => 'project_milestones',
		'functions' => 'index'
	),
	'fields' => array('name_eng','name_fre')
));
$langCode = Configure::read('Config.langCode');
$language = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
$widget_title = !empty( $widget_title) ? $widget_title : $titleMenu['Menu'][$language];
$widget_title = !empty( $widget_title) ? $widget_title : __('Milestones', true);
$canModified = isset( $canModified ) ? $canModified : '0';
?>
<style>
	#milestone-slider{
		padding: 0 25px;
	}
	#milestone-slider *{
		box-sizing: border-box;
	}
	#milestone-slider .milestones-item{
		top: 0;
		padding: 20px 0 10px;
	}
	.milestones-item .date-milestones:before{
		top: 30px;
	}
	#milestone-slider .milestones-item .date-milestones{
		background-color: transparent;
		border: 1px solid #538fFa;
		color: #538fFa; 
		width: 60px;
		height: 60px;
		padding-top: 7px;

	}
	#milestone-slider .milestones-item .date-milestones > span{
		font-size: 20px;
		line-height: 18px;
		color: inherit;

	}
	#milestone-slider .milestones-item .date-milestones span + span{
		font-size: 13px;
		font-weight: 600; 
	}
	#milestone-slider.wd-slick-slider .milestones-item > p {
		margin-top: 6px;
		color: inherit;
		font-weight: 400;
		font-size: 13px;
		max-width: 100px;
		margin-left: auto;
		margin-right: auto;
		position: relative;
	}
	#milestone-slider .milestones-item .date-milestones:before{
		top: 49px;
		background-color: transparent;
		border-top: 2px solid #538fFa;
		width: calc(100% - 60px);
		left: inherit;
		transform: translateX(-50%);
		right: 60px;
		position: absolute;
		z-index: 2;
	}
	#milestone-slider .milestones-item.has-year{
		border-right: 2px dotted #F2F5F7;
	}
	#milestone-slider .milestones-item.has-new-year{
		border-left: 2px dotted #CCC;
	}
	.flash-milestone .flash-field-content {
		padding: 0 20px;
	}
	.wd-table{
		min-height: 600px;
	}
	body{
		overflow: auto;
	}
	.milestone-slider-container *{
		box-sizing: border-box;
	}
	button.slick-arrow:before{
		content: '';
		position: relative;
		display: inline-block;
		vertical-align: middle;
		height: 100%;
		width:0;
	}
	button.slick-arrow{
		width: 20px;
		height: 20px;
		text-align: center;
		top: 50px;
		margin-top: -10px;
		transform: inherit;
	}
	button.slick-arrow..slick-prev{
		left: 0;
	}
	button.slick-arrow..slick-next{
		right: 0;
	}
	button.slick-arrow span{
		display: inline-block;
		vertical-align: middle;
		position: relative;
	}
	button.slick-arrow .img-hover{
		position: absolute;
		opacity: 0;
		transition: all 0.3s ease;
		left: 0;
	}
	button.slick-arrow:hover .img-hover{
		opacity: 1;
	}

	#milestone-slider .milestones-item.milestone-validated .date-milestones{
		border-color: #6EAF79;
		color: #6EAF79;
		z-index: 2;
	}
	#milestone-slider .milestones-item.milestone-validated .date-milestones:before{
		border-top-color: #6EAF79; ;
	}
	#milestone-slider .milestones-item.milestone-blue .date-milestones{
		border-color: #247FC3;
		color: #247FC3;
	}
	#milestone-slider .milestones-item.milestone-blue .date-milestones:before{
		border-top-color: #247FC3;
	}
	#milestone-slider .milestones-item.milestone-orange .date-milestones,
	#milestone-slider .milestones-item.milestone-red .date-milestones{
		border-color: #C34A2E;
		color: #C34A2E;
	}
	#milestone-slider .milestones-item.milestone-orange .date-milestones:before,
	#milestone-slider .milestones-item.milestone-red .date-milestones:before{
		border-top-color: #C34A2E;
	}
	#milestone-slider .milestone-year {
		position: absolute;
		right: 0;
		top: 0;
		top: 85px;
		transform: rotate(-90deg);
		height: 50px;
		line-height: 50px;
		font-size: 50px;
		color: #F2F5F7;
		font-weight: 700;
		margin-top: -41px;
		width: 130px;
		margin-right: -32px;
	}
	#milestone-slider .milestone-new-year {
		position: absolute;
		left: 0;
		top: 0;
		top: 85px;
		transform: rotate(90deg);
		height: 50px;
		line-height: 50px;
		font-size: 50px;
		color: #DDD;
		font-weight: 700;
		margin-top: -41px;
		width: 130px;
		margin-left: -40px;
		z-index: -1;
	}
	.widget_content{
		position: relative;
	}
	.add-new-item{
		position: absolute;
		width: 30px;
		height: 30px;
		border: 1px solid #538fFA;
		bottom: -10px;
		right: 20px;
		background-color: #538fFA;
		border-radius: 50%;
		transition: all 0.3s ease;
		z-index: 1;
	}
	.add-new-item:before, .add-new-item:after {
		content: '';
		width: 2px;
		height: 14px;
		margin-top: 7px;
		margin-left: 13px;
		background-color: #fff;
		position: absolute;
		transition: all 0.2s ease;		
	}
	.add-new-item:after {
		width: 14px;
		height: 2px;
		margin-top: 13px;
		margin-left: 7px;
	}
	.add-new-item:hover{
		opacity: 0.8;
	}
	.wd-widget.milestone-widget >.wd-widget-inner >.widget_content{
		padding: 0 20px;
	}
	.milestone-popup-container{
		position: fixed;
		width: 100vw;
		height: 100vh;
		top: 0;
		left: 0;
		background-color: rgba( 255,255,255,0.40);
		overflow-y: auto;
		z-index: 99;
	}
	.milestone-popup-container-inner{
		position: absolute;
		width: 100%;
		height: 100%;
		padding: 40px;
		text-align: center;
	}
	.milestone-popup-container-inner:before{
		content: '';
		width: 0;
		height: 100%;
		display: inline-block;
		vertical-align: middle;
	}
	.milestone-popup-content{
		display: inline-block;
		vertical-align: middle;
	}
	.wd-widget-popup form label{
		display: block;
		color: #242424;	
		font-size: 14px;
		font-weight: 600;
		line-height: 28px;
	}
	.wd-widget-popup form input{
		display: block;
		height: 50px;
		line-height: 48px;
		padding: 0 15px;
		border: 1px solid #E0E6E8;
		transition: all 0.3s ease;
		background-color: #fff;
		color: #242424;
		width: 280px;
		max-width: 100%;
		margin-bottom: 12px;
	}
	
	.wd-widget-popup form input:focus{
		border-color: #64a3c7;
	}
	.wd-widget-popup form input.wd-date{
		background: #fff url(/img/new-icon/date.png) center right 15px no-repeat;
		padding-right: 40px;
	}
	.wd-widget{
		text-align: left;
	}
	form .btn-form-action{
		font-size: 14px;
		line-height: 22px;
		font-weight: 600;
		text-transform: uppercase;	
		color: #fff;
		border: none;
		padding: 14px 17px;
		background-color: #C6CCCF;
		transition: all 0.3s ease;
		border-radius: 3px;
		text-decoration: none;
		background-size: 250%;
		background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#217FC2 64%,#217FC2 100%);
		display: inline-block;
	}
	form .btn-form-action:hover{
		background-color: #217FC2;
		background-position: right center;
	}
	form .btn-form-action.btn-ok{
		background: #217FC2;
		padding-left: 22px;
		padding-right: 22px;
	}
	form .btn-form-action.btn-ok:hover{
		opacity: 0.95;
	}
	.btn-right {
		float: right;
	}
	form .button-group{
		margin-top: 20px;
	}
	#milestone-slider .milestones-item{
		background: none;
		min-height: 142px;
	}
	#milestone-slider .slick-track{
		margin-left: 0;
	}
	#milestone-slider .slides .wd-slider-item{
		width: 170px;
		float: left;
	}
	.kanban-box .wd-list-assign ul li{
		display: inline-block;
	}
</style>
<div class="wd-widget milestone-widget">
	<div class="wd-widget-inner">
		<div class="widget-title">
			<h3 class="title"> <?php echo $widget_title; ?> </h3>
		</div>
		<div class="widget_content">
			<div id="milestone-slider" class="wd-slick-slider">
				<div class="slides">
					<?php 
						$i = 0; 
						$next_ms = '';
						$min='99999999999';
						$active_ms ='';
						$currentDate = strtotime(date('d-m-Y', time()));
						$compare_year = '';
						foreach ($projectMilestones as $p) { 
							if(($p['milestone_date'] != '0000-00-00') && ($p['milestone_date'] != '')){
								$milestone_date = strtotime($p['milestone_date']);
								$flag = abs($currentDate - $milestone_date);
								$milestone_year = new DateTime();
								$milestone_year->setTimestamp($milestone_date);
								$milestone_year = $milestone_year->format('Y');
								if( ($compare_year && $milestone_year != $compare_year) || $i == 0 || ($compare_year == '')){
									$projectMilestones[$i]['year'] = $milestone_year;
								}
								$compare_year = $milestone_year;
								if($min > $flag && $milestone_date <= $currentDate){
									$min = $flag;
									$active_ms = $p['id'];
								}
							}
							$i++;
						}
					
						$current_item = 0;
						$i = 0;
						foreach ($projectMilestones as $p) {
							if(($p['milestone_date'] != '0000-00-00') && ($p['milestone_date'] != '')){
								$milestone_date = strtotime($p['milestone_date']);
								$nearDate = $currentDate - $milestone_date;
								$item_class = '';
								if( !empty( $p['year']) ){
									// $item_class .= ' has-year';
									$item_class .= ' has-new-year';
								}
								if( $current_item ){
									 $item_class .= ' next-item';
									 $current_item = 0;
								}
								if ($active_ms == $p['id']) {
									$item_class .= ' active-item';
									$current_item = 1;
								}else{
									if($milestone_date > $currentDate){
										$item_class .= ' last-item flag-item';
									}
								}
								if($p['validated']){
									$item_class .= ' milestone-validated';
								}else{
									if ($milestone_date < $currentDate) {
										$item_class .= ' milestone-mi milestone-red';
									} else if($milestone_date > $currentDate) {
										$item_class .= ' milestone-blue';
									} else {
										$item_class .= ' milestone-orange';
									}
								}
								if($milestone_date < $currentDate) { $item_class .= ' out_of_date'; }
								?>
									<div data-num = <?php echo $i; ?> class="wd-slider-item">
										<div class="milestones-item <?php echo $item_class; ?>" data-id="<?php echo $p['id']; ?>">
											<?php
											if( !empty( $p['year']) ){ ?>
												<div class="milestone-new-year">
													<?php echo $p['year'];?> 
												</div>
											<?php } ?> 
											<div class="date-milestones">
												<span><b><?php echo date("d", strtotime($p['milestone_date'])); ?></b></span>
												<span><?php echo __(date("M", strtotime($p['milestone_date'])),true); ?></span>
											</div>
											<p><?php echo $p['project_milestone']; ?></p>
											
										</div>
									</div>
								<?php 
								$i++;
							}
						}
					?>
				</div>
			</div>
			<?php if( $canModified ) { ?>
				<div class="milestone-addnew">
					<a class="add-new-item" href="javascript:void(0);">
						
					</a>
					<div class="popup-container milestone-popup-container" style="display: none;" >
						<div class="milestone-popup-container-inner">
							<div class="milestone-popup-content wd-widget wd-widget-popup">
								<div class="widget-title">
									<h3 class="title"> <?php __('Create new milestone'); ?> </h3>
									<a href="javascript:void(0);" class="popup-close-btn"></a>
								</div>
								<div class="widget_content loading-mark">
									<?php 
									echo $this->Form->create('Milestone', array(
										'type' => 'POST',
										'id' => 'newMilestonewidgetform',
										'class' => 'newMilestonewidgetform',
										'url' => array('controller' => 'project_milestones_preview', 'action' => 'update', $projectName['Project']['id'])
									));
									echo $this->Form->input('project_id', array('type' => 'hidden' , 'value' => $projectName['Project']['id']));
									?>
									<?php 
									echo $this->Form->input('project_milestone', array('type' => 'text', 'label' => __('Milestone Name', true), 'value' => ''));
									echo $this->Form->input('milestone_date', array('type' => 'text', 'autocomplete' => 'off', 'label' => __('Milestone day', true), 'value' => '', 'class' => 'wd-date wd-datepicker', 'data-date_format' => 'dd-mm-yy'));
									echo '<div class="button-group">';
									echo $this->Form->button( __('btnCancel', true), array('type' => 'button', 'class' => 'btn-form-action btn-cancel', 'id'=>'cancelBtn' ));
									echo $this->Form->button(__('btnSave', true), array('type' => 'submit', 'class' =>'btn-form-action btn-ok btn-right' ));
									echo '</div>';
									echo $this->Form->end();
									?>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			<?php } ?> 
		</div>
	</div>
</div>	

<script>
	var canModified = <?php echo json_encode($canModified); ?>;
	$('.wd-date').each(function(){
		$(this).datepicker({
			dateFormat : $(this).data('date_format') ? $(this).data('date_format') : 'dd-mm-yy',
		});
	});
	function milestones_slider(){
        var _slider = $('#milestone-slider .slides');
        var active_index = 0, index=0;

        if( _slider.length == 0) return;
        var item = _slider.children('.wd-slider-item').length;
        if( item){
            _slider.children('.wd-slider-item').each(function(){
                if( $(this).find('.milestones-item').hasClass('active-item')) active_index = index;
                index++;
            });
        }
        var slider_show = 9;
		if($('.milestone-widget').width() < 992){
			slider_show = Math.min(item,5);
		}
        if(item <= slider_show){
            active_index = 0;
        }
        var slick_slider = _slider.slick({
            infinite: false,
            slidesToShow: slider_show,
            //slidesToScroll: slider_show,
            speed: 600,
            arrows: true,
            dots: false,
            //centerMode: true,
            focusOnSelect: true,
            initialSlide: active_index,
            centerPadding: '0',
            prevArrow: '<button type="button" class="slick-prev"><span><img src="/img/new-icon/arrow-left-gray.png"><img class="img-hover" src="/img/new-icon/arrow-left-blue.png"><span></button>',
            nextArrow: '<button type="button" class="slick-next"><span><img src="/img/new-icon/arrow-right-gray.png"><img class="img-hover" src="/img/new-icon/arrow-right-blue.png"></span></button>',
            responsive:[
                {
                    breakpoint: 1440,
                    settings: {
                        slidesToShow: Math.max(slider_show-1, 4),
                    }
                },
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: Math.max(slider_show-2, 3),
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: Math.max(slider_show-3 , 3),
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: Math.max(slider_show-3 , 2),
                    }
                },
            ]
        });
    }
    milestones_slider();
	if( canModified ) {
		$('#milestone-slider').on('click', '.milestones-item', function(e){
			$('#milestone-slider, .wd-table-container').addClass('loading');
			var _this = $(this);
			var color = '';
			$('#milestone-slider').find('.milestones-item').removeClass('active-item');
			$('#milestone-slider').find('.flag-item').addClass('last-item');
			var out_date = _this.hasClass('out_of_date');
			if( !_this.hasClass('milestone-validated')){
				color = 'green';
				_this.removeClass('milestone-blue milestone-mi milestone-red milestone-orange').addClass('milestone-validated');
			}else{
				if( out_date){
					color = 'mi';
					_this.removeClass('milestone-blue milestone-green milestone-validated milestone-orange').addClass('milestone-mi milestone-red');
				}else{
					color = 'blue';
					_this.removeClass('milestone-green milestone-validated milestone-mi milestone-red milestone-orange').addClass('milestone-blue');
				}
			}
			$(this).removeClass('last-item');
			$(this).addClass('active-item');
			var _item_id = $(this).data('id');
			$.ajax({
			   url : "<?php echo $html->url('/project_milestones_preview/change_milestone_status/'.$projectName['Project']['id']); ?>" + '/' + _item_id,
				type : 'GET',
				data : '',
				success : function(respons){
					respons = $.parseJSON(respons);
					var success = respons['result'];
					if( success){
						var item = respons.data.ProjectMilestone;
						var item_id = item.id;
						console.log(item.validated );
						var data = [], selectedRows = 0;
						
						// Viet add code last-update here
						$('#milestone-slider, .wd-table-container').removeClass('loading');
					}
					else{
						location.reload();
					}
				},
				error: function(){
					location.reload();
				}
			});
		});
		$('.milestone-addnew .add-new-item').on('click', function(e){
			$('.milestone-popup-container').fadeToggle('300');
			$(this).closest('.wd-row').addClass('wd-on-top');
		});
	}
	$('#cancelBtn, .popup-close-btn').on('click', function(e){
		$(this).closest('.popup-container').fadeOut('300');
		$('.wd-row').removeClass('wd-on-top');
	});
	$('.newMilestonewidgetform').on('submit', function(e){
		var _this = $(this);
		var loading_mark = _this.closest('.loading-mark');
		e.preventDefault();
		loading_mark.addClass('loading');
		var _data = new FormData(this);
		var _url = $(this).attr('action');
		$.ajax({
			type: 'POST',
			url: _url,
			data: _data,
			cache: false,
			processData: false,
			contentType: false,
			success: function(responseContent){
				var data = JSON.parse(responseContent);
				if( data.result == true){
					var _cont = $('#milestone-slider');
					var _slider = $('#milestone-slider .slides');
					_slider.height(_slider.height());
					var _slider_loading_mark = _cont.closest('.loading-mark');
					_slider_loading_mark.addClass('loading');
					try{
					_slider.slick('unslick');
					}catch(e){
						console.log(e.message);
						location.reload();
					}
					_slider.css('overflow', 'hidden');
					$.ajax({
						url: "<?php echo $html->url(array('controller' => 'project_milestones_preview', 'action' => 'get_milestone_slider', $projectName['Project']['id']));?>",
						type : 'GET',
						data : '',
						success : function(respons){
							if( respons) {
								_slider.empty().html(respons);
								_slider.height('');
								milestones_slider();
								_slider.css('overflow', '');
								_slider_loading_mark.removeClass('loading');
							}else{
								location.reload();
							}
						},
						complete: function(){
							_slider_loading_mark.removeClass('loading');
						},
						error: function(){
							location.reload();
						}
					});
				}else{
					if( data.message ){
						if( $('#flashMessage').length) $('#flashMessage').remove();
						$(data.message).insertBefore('.indicator-layout:first');
						setTimeout(function(){
							$('#flashMessage').fadeOut('300', $('#flashMessage').remove());
						} , 3000);
					}
					
				}
			},
			complete: function(){
				loading_mark.removeClass('loading');
				_this.closest('.popup-container').fadeOut('300');
				$('.wd-row').removeClass('wd-on-top');
			},
			error: function(){
				location.reload();
			}
		});
	});
</script>
