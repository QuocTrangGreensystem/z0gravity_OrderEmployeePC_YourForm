<?php 
    echo $html->css(array(
        'preview/global-views',
        'jquery.fancybox'
    ));
    echo $html->script(array(
        'jquery.fancybox.pack'
    ));
 ?>
<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->script('dropzone.min'); 

$canModified = (!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite);
?>
<style>
body #layout{
	background: #f2f5f7;
}
</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
                    <div class="wd-section <?php echo $projectGlobalView ? 'show-menu' : ''; ?>" id="wd-fragment-1">
                        
                        <fieldset>
                            <?php if ($projectGlobalView) : ?>
                             <!-- If is_file = 1 then File else if is_file = 0 then Url -->
                                <div id="download-place">
                                    <a class="download-place-toggle-button" href="javascript:void(0);" title=" <?php echo __d(sprintf($_domain, 'Global_Views'), 'More option', true); ?>" data-closetitle="<?php echo __d(sprintf($_domain, 'Global_Views'), 'Close', true); ?>" data-viewmoretitle="<?php echo __d(sprintf($_domain, 'Global_Views'), 'More option', true); ?>">
                                        <i class='button-dot button-dot-1'></i>
                                        <i class='button-dot button-dot-2'></i>
                                        <i class='button-dot button-dot-3'></i>
                                    </a>
                                    <div class="download-place-inner wd-title">
                                    <?php
                                    if($projectGlobalView['ProjectGlobalView']['is_https'] && $projectGlobalView['ProjectGlobalView']['is_file']){
                                        if($projectGlobalView['ProjectGlobalView']['is_file']){
                                        echo $this->Html->link($this->Html->image('new-icon/download.png') . __('', true), array(
                                                    'action' => 'attachment', $projectName['Project']['id'], '?' => array('download' => true, 'sid' => $api_key)), array(
                                            'escape' => false,
                                            'id' => 'download-attachment',
                                            'class' => 'btn',
                                            'title' => __d(sprintf($_domain, 'Global_Views'), 'Download this attachment ', true)
                                            ));
                                        }else{
                                            $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                                            $IFRAME = $is_http.$projectGlobalView['ProjectGlobalView']['attachment'] ;
                                            echo "<a href=".$is_http.$projectGlobalView['ProjectGlobalView']['attachment']." target='_blank'>".$this->Html->image('url.png') . __('URL ', true)."</a>";
                                        }
                                    }
                                    if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) {
                                        echo $this->Html->link($this->Html->image('new-icon/delete.png') . __('', true), 'javascript:void(0);', array(
                                            'escape' => false,
                                            'id' => 'replace-attachment',
                                            'class' => 'btn',
                                            'title' => __d(sprintf($_domain, 'Global_Views'), 'Remove this attachment', true)
                                            ));
                                        echo $html->image('ajax-loader.gif', array(
                                            'id' => 'loader',
                                            'style' => 'display: none; margin-left: 3px'
                                        ));
                                    }
									echo $this->Html->link($this->Html->image('new-icon/expand.png') . __('', true), 'javascript:void(0);', array(
                                            'escape' => false,
                                            'id' => 'expand',
                                            'class' => 'btn',
                                            'title' => __d(sprintf($_domain, 'Global_Views'), 'Expand', true)
                                            ));
                                    ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php /*
                            <div id="upload-place" class="wd-submit" style="width: auto;padding: 0;margin: 0 0 0 20px; vertical-align: middle; <?php echo 'display:' . ($projectGlobalView ? 'none' : 'block') ?>">
                                <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                                     <div class="wd-input wd-normal" style="float: left;width: auto; margin-top: 5px; clear: none; margin-right: 5px;">
                                        <?php
                                            $options=array( 1 => __('File',true), 0 => __('URL',true), 2 => __('HTML',true));
                                            $attributes=array('legend'=>false,'class'=>'r-right','div'=>true,'default'=>1);
                                            echo $this->Form->radio('is_file',$options,$attributes);
                                        ?>  
                                    </div>
                                    <div class="wd-input" style="float: left;width: auto; clear: none; margin-right: 5px;">
                                        <?php
                                        // echo $this->Form->input('attachment', array('div' => false, 'label' => false,
                                        //     'type' => 'file',
                                        //     'name' => 'FileField[attachment]',
                                        //     'style' => 'width: 200px'
                                        // ));
                                        ?>
                                    </div>
                                    <div class="wd-input" style="float: left;width: auto; clear: none; margin-right: 5px;">
                                        <?php
                                        echo $this->Form->input('attachment', array('div' => false, 'label' => false,'id'=>'attachmentUrl',
                                            'type' => 'text',
                                            'style' => 'width: 200px; padding: 6px',
                                            'placeholder' => 'Ex: www.example.com'
                                        ));
                                        ?>
                                    </div>
                                    <div class="wd-input" style="float: left;width: auto; clear: none; margin-right: 5px;">
                                        <?php
                                        echo $this->Form->input('attachment', array('div' => false, 'label' => false,'id'=>'attachmentHtml',
                                            'type' => 'textarea',
                                            'style' => 'width: 200px; padding: 6px',
                                        ));
                                        ?>
                                    </div>
                                    <div class="wd-button" style="float: left;margin-right: 5px;">
                                        <button type="submit" class="btn btn-save" id="btnSave">
                                            <span><?php __('Save') ?></span>
                                        </button>
                                    </div>
                                    <div id="file-types">
                                        <?php __('Allowed file types') ?>: <span><?php echo str_replace(',', ', ', $allowedFiles) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            */
							?>
							
                            <div id="upload-place" class="wd-upload" style="<?php echo 'display:' . ($projectGlobalView ? 'none' : 'block'); ?>">
								<?php if( $canModified ) { ?>
									<div class="wd-tab wd-uload-tabs">
										<nav class="wd-item tabs-header-container">
											<ul class="tabs-upload-header-inner">
												<li class="tab-header wd-current" data-tab="0"><?php echo __('File',true); ?></li>
												<li class="tab-header" data-tab="1"><?php echo __('URL',true); ?></li>
												<li class="tab-header" data-tab="2"><?php echo __('HTML',true); ?></li>
											</ul>
										</nav>
										<div class="tabs-content-container">
											<ul class="tabs-content-container-inner">
												<li class="tab-content wd-current" data-tab="0">
													<div class="trigger-upload" style="<?php echo 'display:' . ($projectGlobalView ? 'none' : 'block') ?>">
														<form id="upload-widget" onsubmit="completeAndRedirect()" method="post" action="/project_global_views_preview/upload/<?php echo $projectName['Project']['id']; ?>" class="dropzone" value="" >
															<input type="hidden" name="data[ProjectGlobalView][is_file]" rel="no-history" value="1" id="UploadId">
														</form>
													</div>
													<br style="clear: both;" />
												</li>
												<li class="tab-content" data-tab="1">
													
													<?php 
													echo $this->Form->create('ProjectGlobalView', array(
														'type' => 'file',
														'id' => 'ProjectGlobalView',
														'url' => array('controller' => 'project_global_views', 'action' => 'upload',
															$projectName['Project']['id'])));
												   
													echo $this->Form->input('attachmentURL', array('div' => false, 'label' => false,'id'=>'attachmentUrl',
														'type' => 'text',
														//'style' => 'width: 200px; padding: 6px',
														'placeholder' => 'Ex: www.example.com/your_image.jpg'
													)); 
													?>
													<fieldset>
														<?php
														echo $this->Form->input('is_file', array(
															//'div' => false, 
															'label' => false,
															'id'=>'uploadURL',
															'type' => 'hidden',
															'value' => '0',
														)); 
														?>
														
														<div class="wd-submit">
															<button type="submit" class="btn btn-submit wd-button-f" id="btnSave">
																<?php __('Submit') ?>
															</button>
														</div>
													</fieldset>

													<?php echo $this->Form->end(); ?>
													

												</li>
												<li class="tab-content" data-tab="2">
													 <?php 
													echo $this->Form->create('ProjectGlobalView', array(
														// 'type' => 'file',
														'id' => 'ProjectGlobalView_uploadIframe',
														'url' => array('controller' => 'project_global_views', 'action' => 'upload',
															$projectName['Project']['id'])));
												   
													echo $this->Form->input('attachment', array('div' => false, 'label' => false,'id'=>'attachmentHtml',
														'type' => 'textarea',
														'placeholder' => 'Enter embed code here'
													)); 
													echo $this->Form->input('is_file', array('div' => false, 'label' => false,'id'=>'uploadIframe',
														'type' => 'hidden',
														'value' => '2',
													)); 
													?>
													<fieldset>
														<div class="wd-submit">
																<button type="submit" class="btn btn-submit wd-button-f" id="btnSave">
																	<?php __('Submit') ?>
																</button>
														</div>
													</fieldset>

													<?php echo $this->Form->end(); ?>
												</li>
											</ul>
									</div>
								<?php }else{ ?>
									<p class="empty-message">
										<?php __('No Global View was upload'); ?>
									</p> 
								<?php } ?> 

                            </div>
                        </fieldset>
                        
                    </div>
                    <br  />
                    <div class="wd-section" id="wd-fragment-2" >
                        <?php 

                       
                        $isDoc = false;
                        $link = $this->Html->url(array('action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
                        $view_by_frame = false;
						if ($projectGlobalView) {
							$is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
							if($noFileExists){
								// LINK
								
								$link = $projectGlobalView['ProjectGlobalView']['attachment'] ;
								preg_match( '/(<iframe)|(http)/', $link, $matches);
								if( empty($matches)) $link = $is_http.$link;
								$view_by_frame = true;
							}else if($projectGlobalView['ProjectGlobalView']['is_file'] == 0){
								// isDoc
								$link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
								$view_by_frame = true;
							}else if(!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
								// File upload is not image.
								$link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
								$view_by_frame = true;
							}
						} else {
							$link = '';
						}
                        
                        if($view_by_frame){ 
							if( preg_match( '/<iframe/', $link) ) {
								echo $link;
							}else{ ?>
								<iframe src="<?php echo $link; ?>" style="width: 100%;height: 900px;"></iframe>
							<?php } ?> 	  
                        <?php } else{ ?>
							<img src="<?php echo $link; ?>"></img>
                            <a href="<?php echo $link; ?>" class="fancy image" rel="one_pic_expand" data-fancybox="image" data-type="image" id="on-image-expand-btn" tabindex="0"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
<script type="text/javascript">
var wdTable = $('#wd-fragment-2');
if(wdTable.find('iframe').length != 0){
    var heightTable = $(window).height() - wdTable.offset().top - 40;
    wdTable.css({
        height: heightTable,
    });
    $(window).resize(function(){
        heightTable = $(window).height() - wdTable.offset().top - 40;
        wdTable.css({
            height: heightTable,
        });
    });
}

function expandScreen(){
    $('#table-control').hide();
    $('.wd-title').hide();
    $('#wd-fragment-2').addClass('fullScreen');
    $('#collapse').show();
    $('#on-image-expand-btn').hide();
    $(window).resize();
}
function collapseScreen(){
    $('#table-control').show();
    $('.wd-title').show();
    $('#collapse').hide();
    $('#on-image-expand-btn').show();
    $('#wd-fragment-2').removeClass('fullScreen');
    $(window).resize();
}
    (function($){
        $(function(){
            $('#replace-attachment').click(function(){
                $('#loader').show();
                $.ajax({
                    type : 'POST',
                    url : '<?php echo $html->url('/project_global_views/delete/' . @$projectGlobalView['ProjectGlobalView']['id']) ?>',
                    success : function(){
                        $('#loader').hide();
                        $('#download-place').remove();
                        $('#upload-place').show();
                        $('iframe').prop('src', 'about:blank');
                        $('#wd-fragment-1').removeClass('show-menu');
                        $('#wd-fragment-2').html('');
                        $('.trigger-upload').css('display', 'block');
                    }
                });
            });
        });
    })(jQuery);
    $('.wd-tab >nav .tab-header').on('click', function(){
        var _this = $(this);
        var _index = _this.data('tab');
        _this.addClass('wd-current').siblings().removeClass('wd-current');        
        var _tabs_content = _this.closest('.wd-tab').find('.tabs-content-container-inner:first').children('.tab-content');
        _tabs_content.fadeOut(300);
        _tabs_content.each(function(){
            var _this = $(this);
            console.log(_this.data('tab'), _index);
            if(_this.data('tab') == _index){
                _this.addClass('wd-current').siblings().removeClass('wd-current');
                _this.fadeIn(300);

            }
        });

    });
    openImage = function(element){
        var t = $(element);
        var url = t.attr('src');
        window.open(url, '_blank');
    }
    $('.download-place-toggle-button').on('click', function(){
        var _this = $(this);
        if( _this.hasClass('active')){
            _this.siblings('.download-place-inner').removeClass('open').fadeOut(500);
            _this.removeClass('active');
        }else{
            _this.siblings('.download-place-inner').addClass('open').show();
            _this.addClass('active');
        }
    });
    $('.fancy.image').fancybox({
        type: 'image'
    });

    Dropzone.autoDiscover = false;
    $(function() {
        var dropzone_container = $('.trigger-upload');
        var radio_input = $('#ProjectGlobalViewIndexForm input[type="radio"]');
        var input_change = $('input#ProjectGlobalViewIsFile1');
        input_change.closest('form').find('.wd-button').hide();
        radio_input.on('click', function(){
            if( input_change.is(':checked')) {
                dropzone_container.fadeIn(300);
                input_change.closest('form').find('[type="submit"]').hide();

            }
            else {
                dropzone_container.hide();
                input_change.closest('form').find('[type="submit"]').show();
                input_change.closest('form').find('.wd-button').show();
            }
        });
		if($('#upload-widget').length > 0){
			var myDropzone = new Dropzone("#upload-widget",{maxFiles: 1});
			myDropzone.on("success", function(file) {
				myDropzone.removeFile(file);
				location.reload();
			});
		}
    });
</script>
    