<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->css('preview/setting-upload'); ?>
<?php echo $html->script('jscolor'); ?>
<?php echo $html->script('dropzone.min'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php $employee_info = $this->Session->read("Auth.employee_info"); ?>

<style>
	input.jscolor{
		max-width: 125px;
	}
	fieldset div.wd-input, fieldset div.wd-input input, fieldset div.wd-input1 input{
	 float: none;
	}
	#file-types{
	   margin-left: 150px;
	}
	fieldset{
		margin-bottom: 20px;
	}
	#btnSave{
		margin-top: 50px;
		margin-left: 140px;
	}
	.file-item + .file-item.empty{
		display: none;
	}
	.file-item.error span{
		color: red;
	}
	.wd-list-project .wd-tab .wd-content label{
		float: none;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                        
                            <?php if (($employee_info['Employee']['is_sas'] == 1)){
                            ?>
                                <div class="content-setting-upload">
                                    <label for="last-name"><?php __("Picture login screen") ?></label>
                                    <div class="setting-image-upload">
                                        
                                        <form id="upload-widget" method="post" action="/colors/upload" class="dropzone" value="" >
                                            <input type="hidden" name="data[Color][attachment]" rel="no-history" value="1" id="UploadId">
                                            <input type="hidden" name="data[Color][is_file]" rel="no-history" value="1" id="UploadId">
                                        </form>
                                        <div class="file-upload">
                                            <?php foreach( $colors as $color){ 
											if( !empty( $color['Color']['attachment'])) { ?>
                                            <div class="file-item">
                                                <i class="icon-paper-clip"></i><span><?php if(!empty($color['Color']['attachment'])) echo $color['Color']['attachment']; ?></span>
                                                <a title="Delete file" data-id = "<?php echo $color['Color']['id']; ?>" onclick="deleteAttachment.call(this)"><img src="/img/new-icon/delete-attachment.png" ></a>
                                             </div>
                                            <?php } } ?>
                                            <div class="file-item empty">
                                                <span><?php __('You have not uploaded any file.') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else{ ?>
                                <!-- Dropzone background header newdesign -->
                                <div class="content-setting-upload">
                                    <label for="last-name"><?php __("Image top menu/ Picture top menu: (1440*280)") ?></label>
                                    <div class="setting-image-upload">
                                        <form id="upload-widget" method="post" action="/colors/upload/<?php echo $employee_info['Company']['id']; ?>" class="dropzone" value="" >
                                            <input type="hidden" name="data[Color][attachment_background]" rel="no-history" value="1" id="UploadId">
                                            <input type="hidden" name="data[Color][is_file]" rel="no-history" value="1" id="UploadId">
                                        </form>
                                        <div class="file-upload">
                                            <?php 
											// debug( $colors); exit;
											foreach( $colors as $color){
												if( !empty( $color['Color']['attachment_background'])) { ?>
                                            <div class="file-item">
                                                <i class="icon-paper-clip"></i><span><?php if(!empty($color['Color']['attachment_background'])) echo $color['Color']['attachment_background']; ?></span>
                                                <a title="Delete file" data-id = "<?php echo $color['Color']['id']; ?>" onclick="deleteAttachment.call(this)"><img src="/img/new-icon/delete-attachment.png" ></a>
                                             </div>
											<?php } }?>
                                            <div class="file-item empty">
                                                <span><?php __('You have not uploaded any file.') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
                            <br style="clear: both;" />
                                
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var wdTable = $('#wd-fragment-1');

    // (function($){
    //     $(function(){
    //         $('#replace-attachment').click(function(){
    //             $('#loader').show();
    //             $.ajax({
    //                 type : 'POST',
    //                 url : '<?php echo $html->url('/companies/delete/' . @$company_id) ?>',
    //                 success : function(){
    //                     $('#loader').hide();
    //                     $('#download-place').remove();
    //                     $('#upload-place').show();
    //                     $('iframe').prop('src', 'about:blank');
    //                     $('#wd-fragment-2').html('');
    //                 }
    //             });
    //         });
    //     });
    // })(jQuery);
    $("#attachmentUrl").parent().hide();
    $('#CompanyIsFile1, #CompanyIsFile0').click(function(){
       if($('#CompanyIsFile1').is(':checked')) {
            $("#CompanyAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
            $('#file-types').show();
        } else {
            $("#CompanyAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
            $('#file-types').hide();
        }
    });
    function deleteAttachment(){
        var file_id = $(this).data('id');
        itemPic = $(this).closest('.file-item');
		itemPic.slideToggle(300);
        $.ajax({
            type : 'POST',
            url : '/colors/delete/'+ file_id,
            success : function(data){
				if( data == 1){
					setTimeout(function(){
						itemPic.remove();
					}, 300);
				}else{
					itemPic.slideToggle(300).addClass('error');
				}
            },
			error: function(){
				itemPic.slideToggle(300).addClass('error');
			}
        });
    };
    Dropzone.autoDiscover = false;
    $(function() {
        var myDropzone = new Dropzone("#upload-widget");
        myDropzone.on("success", function(file) {
            // location.reload();
        });
        myDropzone.on("queuecomplete", function(file) {
            location.reload();
        })
    });
   
</script>