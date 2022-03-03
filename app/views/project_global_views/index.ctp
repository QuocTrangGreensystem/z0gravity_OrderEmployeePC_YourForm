<style type="text/css">
    .error-message {
        color: #FF0000;
        margin-left: 35px;
    }
    #download-place img{
        margin-right: 10px;
        vertical-align: middle;
    }
    #replace-attachment{
        /*margin-left: 20px;*/
    }
    #wd-container-main .wd-layout{
        padding-bottom: 10px;
    }
    .wd-input.wd-normal input,
    .wd-input.wd-normal label {
        float: none;
        width: auto;
    }
    .wd-input.wd-normal label {
        display: inline-block;
        margin-left: 5px;
        line-height: 20px;
    }
    fieldset div.wd-input {
        margin: 0;
    }
    #file-types {
        clear: both;
        line-height: 30px;
    }
    #file-types span {
        color: green;
    }
    .wd-section img:hover{
        cursor: pointer;
    }
    .wd-tab .wd-panel{
        border: none;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2" style="float: left;border: 0; margin-right: 20px; color: orange"><?php echo $projectName['Project']['project_name'] ?></h2>
                        <?php
                        echo $this->Form->create('ProjectGlobalView', array(
                            'type' => 'file',
                            'url' => array('controller' => 'project_global_views', 'action' => 'upload',
                                $projectName['Project']['id'])));
                        ?>
                        <fieldset style="float: left;">
                            <?php if ($projectGlobalView) : ?>
                             <!-- If is_file = 1 then File else if is_file = 0 then Url -->
                                <div id="download-place">
                                    <?php
                                    if($projectGlobalView['ProjectGlobalView']['is_https'] && $projectGlobalView['ProjectGlobalView']['is_file']){
                                        if($projectGlobalView['ProjectGlobalView']['is_file']){
                                        echo $this->Html->link($this->Html->image('download.png') . __('', true), array(
                                                    'action' => 'attachment', $projectName['Project']['id'], '?' => array('download' => true, 'sid' => $api_key)), array(
                                            'escape' => false,
                                            'id' => 'download-attachment'));
                                        }else{
                                            $is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
                                            $IFRAME = $is_http.$projectGlobalView['ProjectGlobalView']['attachment'] ;
                                            echo "<a href=".$is_http.$projectGlobalView['ProjectGlobalView']['attachment']." target='_blank'>".$this->Html->image('url.png') . __('URL ', true)."</a>";
                                        }
                                    }
                                    if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) {
                                        echo $this->Html->link($this->Html->image('delete.png') . __('', true), 'javascript:void(0);', array(
                                            'escape' => false,
                                            'id' => 'replace-attachment'));
                                        echo $html->image('ajax-loader.gif', array(
                                            'id' => 'loader',
                                            'style' => 'display: none; margin-left: 3px'
                                        ));
                                    }
                                    ?>
                                    <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen" style="margin-top: -10px"></a>
                                </div>
                            <?php endif; ?>
                            <div id="upload-place" class="wd-submit" style="width: auto;padding: 0;margin: 0 0 0 20px; vertical-align: middle; <?php echo 'display:' . ($projectGlobalView ? 'none' : 'block') ?>">
                                <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                                <?php /*
                                    <div class="wd-tabs-container">
                                        <div class="wb-tabs-container-inner">
                                            <ul class="wd-tabs-header">
                                                <li class="tablinks active" data-tabid="1"> <?php echo __('Upload File',true); ?></li>
                                                <li class="tablinks" data-tabid="2"> <?php echo __('Upload by URL',true); ?></li>
                                                <li class="tablinks" data-tabid="3"> <?php echo __('Use HTML',true); ?></li>    
                                            </ul>
                                            <div class="wd-tabs-content">
                                                <ul class="wb-tab-list-content">
                                                    <li class="tab-content active" data-tabid="1">

                                                    </li>
                                                    <li class="tab-content" data-tabid="2">
                                                    </li>
                                                    <li class="tab-content" data-tabid="3">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    */?>
                                     <div class="wd-input wd-normal" style="float: left;width: auto; margin-top: 5px; clear: none; margin-right: 5px;">
                                        <?php
                                            $options=array( 1 => __('File',true), 0 => __('URL',true), 2 => __('HTML',true));
                                            $attributes=array('legend'=>false,'class'=>'r-right','div'=>true,'default'=>1);
                                            echo $this->Form->radio('is_file',$options,$attributes);
                                        ?>
                                    </div>
                                    <div class="wd-input" style="float: left;width: auto; clear: none; margin-right: 5px;">
                                        <?php
                                        echo $this->Form->input('attachment', array('div' => false, 'label' => false,
                                            'type' => 'file',
                                            'name' => 'FileField[attachment]',
                                            'style' => 'width: 200px'
                                        ));
                                        ?>
                                    </div>
                                    <div class="wd-input" style="float: left;width: auto; clear: none; margin-right: 5px;">
                                        <?php
                                        echo $this->Form->input('attachmentURL', array('div' => false, 'label' => false,'id'=>'attachmentUrl',
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
                                    <div style="float: left;margin-right: 5px;">
                                        <button type="submit" class="btn btn-save" id="btnSave">
                                            <span><?php __('Save') ?></span>
                                        </button>
                                    </div>
                                    <div id="file-types">
                                        <?php __('Allowed file types') ?>: <span><?php echo str_replace(',', ', ', $allowedFiles) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </fieldset>
                        <?php echo $this->Form->end(); ?>
                        <br style="clear: both;" />
                    </div>
                    <br  />
                    <div class="wd-section" id="wd-fragment-2" style="overflow: auto">
                        <?php 
                        $check = 1;
						$is_link = false;
						$is_html = false;
						$link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
						if ($projectGlobalView) {
							$is_http = $projectGlobalView['ProjectGlobalView']['is_https'] ? 'https://' : 'http://';
							if($noFileExists){
								// LINK
								if($projectGlobalView['ProjectGlobalView']['is_https'] == 1){
									$link = $is_http.$projectGlobalView['ProjectGlobalView']['attachment'];
									$is_link = true;
								}else{
									$link = $projectGlobalView['ProjectGlobalView']['attachment'];
									$is_html = true;
								}
							}else{
								// File upload is not image.
								if(!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView['ProjectGlobalView']['attachment'])) {
									$link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
									$is_link = true;
								}
							}
						} else {
							$link = '';
						}
						if(!empty($link)){
                            $check = 2;
                        }
						if($is_html){
							echo $link;
						}else if($is_link){
							  echo '<div id="local_link">' . __('Local view : ', true) .
                            $this->Html->link(
                                $link,$link,array('target' => '_blank')
                            ) . '</div>';
						?>
							<iframe src="<?php echo $link; ?>" class="img-responsive" style="width: 100%; height: 900px"></iframe>
						<?php }else{ ?>
							<img class="img-responsive" src="<?php echo (!empty($link) ? $link : '') ?>" alt="">
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
var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 500) ? 500 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
var check = <?php echo json_encode($check) ?>;
// first run
if( check == 2){
    // expandScreen();
}
function expandScreen(){
    $('#table-control').hide();
    $('.wd-title').hide();
    $('#wd-fragment-2').addClass('fullScreen');
    $('#collapse').show();
    $(window).resize();
}
function collapseScreen(){
    $('#table-control').show();
    $('.wd-title').show();
    $('#collapse').hide();
    $('#wd-fragment-2').removeClass('fullScreen');
    $(window).resize();
}
    (function($){
        $(function(){
            $('#replace-attachment').click(function(){
                console.log(1111);
                $('#loader').show();
                $.ajax({
                    type : 'POST',
                    url : '<?php echo $html->url('/project_global_views/delete/' . @$projectGlobalView['ProjectGlobalView']['id']) ?>',
                    success : function(){
                        $('#loader').hide();
                        $('#download-place').remove();
                        $('#upload-place').show();
                        $('iframe').prop('src', 'about:blank');
                        $('#wd-fragment-2').html('');
                    }
                });
            });
        });
    })(jQuery);
    $("#attachmentUrl").parent().hide();
    $('#attachmentHtml').parent().hide();
    $('#ProjectGlobalViewIsFile1, #ProjectGlobalViewIsFile0, #ProjectGlobalViewIsFile2').click(function(){
       if($('#ProjectGlobalViewIsFile1').is(':checked')) {
            $("#ProjectGlobalViewAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
            $('#file-types').show();
            $('#attachmentHtml').parent().hide();
        }else if($('#ProjectGlobalViewIsFile2').is(':checked')) {
            $("#ProjectGlobalViewAttachment").parent().hide();
            $('#attachmentHtml').parent().show();
            $("#attachmentUrl").parent().hide();
        }else {
            $("#ProjectGlobalViewAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
            $('#file-types').hide();
            $('#attachmentHtml').parent().hide();
        }
    });
    openImage = function(element){
        var t = $(element);
        var url = t.attr('src');
        window.open(url, '_blank');
    }
    // $('#ProjectGlobalViewIsFile0').click(function(){
    //    if($('#ProjectGlobalViewIsFile1').is(':checked')) {
    //         $("#ProjectGlobalViewAttachment").parent().show();
    //         $("#attachmentUrl").parent().hide();
    //     } else {
    //         $("#ProjectGlobalViewAttachment").parent().hide();
    //         $("#attachmentUrl").parent().show();
    //     }
    // });
    setTimeout(function(){
        var iframe = $('#wd-fragment-2').width();
        var img = $('#wd-fragment-2').find('img').width();
        var src = $('#wd-fragment-2').find('img').attr('src');
        var left = (iframe - img)/2 - 10;
        var local = $('#local_link');
        var haveIf = $('#wd-fragment-2').find('iframe');
        if((left >= 0) && src){
            $('#wd-fragment-1').css('margin-left', left+'px');
        } else if((left >= 0) && !src && local.length){
            var _l = (iframe - 650)/2;
            $('#wd-fragment-1').css('margin-left', _l + 'px');
            $('#local_link').css('margin-left', _l + 'px');
        } else if(!haveIf.length) {
            $('#wd-fragment-1').css('margin-left', '25%');
        }
        if(src === undefined){
            $('#wd-fragment-2').css('overflow', 'hidden');
        }
    }, 2500);
</script>
