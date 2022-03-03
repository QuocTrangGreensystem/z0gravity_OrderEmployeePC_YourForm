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
		margin-bottom: 0;
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
	.content-about-company{
		margin-top: 20px;
	}
	.content-about-company textarea{
		width: 100%;
		padding: 10px;
		border: 1px solid #ddd;
		box-sizing: border-box;
		margin-bottom: 10px;
	}
	.wd-list-project .wd-tab .wd-content label{
		text-transform: uppercase;
		margin-bottom: 0;
	}
	.wd-list-project .wd-tab .wd-content .field-title{
		margin-bottom: 5px;
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
                                <div class="content-setting-upload">
                                    <label for="last-name"><?php __("Client logo") ?></label>
                                    <div class="setting-image-upload">
                                        
                                        <form id="upload-widget" method="post" action="/colors/upload_logo" class="dropzone" value="" >
                                            
                                        </form>
                                        <div class="file-upload">
											<?php if(!empty( $logo_client['logo'])) { ?>
                                            <div class="file-item">
                                                <i class="icon-paper-clip"></i><span><?php if(!empty($logo_client['logo']['logo_client'])) echo $logo_client['logo']['logo_client']; ?></span>
                                                <a title="Delete file" data-id = "<?php echo $logo_client['logo']['id']; ?>" onclick="deleteLogo.call(this)"><img src="/img/new-icon/delete-attachment.png" ></a>
                                             </div>
                                            <?php }else { ?>
                                            <div class="file-item empty">
                                                <span><?php __('You have not uploaded any file.') ?></span>
                                            </div>
											<?php } ?>
                                        </div>
                                    </div>
                                </div>
								<br style="clear: both;" />
								
                                <div class="content-about-company">
                                    <label for="last-name"><?php __("About company") ?></label>
                                    <div>
										<p class="field-title"><?php __("Frist text") ?></p>
										<textarea rows="5" name="first_text"><?php if(!empty($text_about['first_text'])) echo $text_about['first_text']; ?></textarea>
										<p class="field-title"><?php __("Last text") ?></p>
										<textarea rows="5" name="last_text"> <?php if(!empty($text_about['last_text']))echo $text_about['last_text']; ?></textarea>
                                    </div>
                                </div>
								
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
    function deleteLogo(){
        var file_id = $(this).data('id');
        itemPic = $(this).closest('.file-item');
		itemPic.slideToggle(300);
        $.ajax({
            type : 'POST',
            url : '/colors/delete_logo/'+ file_id,
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
    $('textarea').on('change', function(){
		var name = $(this).attr("name");
		var value = $.trim($(this).val());
		if(value.length > 0){
			$.ajax({
				url: '/colors/update_text/',
				data: {
					data : { name : name, value : value }
				},
				type:'POST',
				success:function(data) {
					
				}
			});
		}
	});
</script>