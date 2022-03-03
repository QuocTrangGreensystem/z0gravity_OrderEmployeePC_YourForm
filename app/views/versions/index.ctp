<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
<style type="text/css">
#VersionContent{height: 120px !important;width: 75% !important;}
#VersionName{width: 25% !important;}
.wd-list-project .wd-tab .wd-content label{
	width: 100px;
}
.wd-content .wd-version{
	margin-bottom: 20px;
}
.wd-content a.wd-reset{
	height: 40px;
    width: 40px;
    line-height: 38px;
    display: none;
    border: 1px solid #E1E6E8;
    background-color: #FFFFFF;
    border-radius: 3px;
    padding: 0;
    box-sizing: border-box;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    color: #666;
	display: inline-block;
}
.wd-content a.wd-reset:hover{
	background-color: #247FC3;
    color: #fff;
}
.wd-content a.wd-reset:before{
	margin-right: 0;
}
.wd-content a.wd-reset:hover:before{
	color: #fff;
}
.wd-content select.version{
	height: 40px;
    width: 100px;
    border: 1px solid #E0E6E8;
    padding: 0 20px 0 10px;
    color: #666666;
    font-family: "Open Sans";
    font-size: 14px;
    line-height: 38px;
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    -o-appearance: none;
    appearance: none;
    background: url(/img/new-icon/down.png) no-repeat right 10px center #fff !important;
}
.version-title, .version-log-title{
	margin-top: 20px;
	margin-bottom: 10px;
	font-size: 16px;
	color: #247FC3;
}
.version-log-title{
	font-size: 14px;
	margin-top: 0;
	margin-bottom: 10px;
}
.wd-version-action{
	margin-top: 20px;
}
.wd-list-project .wd-tab .wd-content label{
	float: none;
}
.wd-content{
	position: relative;
}
.wd-content.loading:before{
	content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255, 0.6) url(/img/business/wait-1.gif) no-repeat center center;
    background-size: 30px;
    z-index: 3;
    display: block;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <div class="wd-content">
								<div class="wd-version-action">
									<a title="<?php echo __('Reset', true); ?>" href="<?php echo $this->Html->url('/versions/updateVersion');?>" class="wd-reset"></a>
									<?php 				
										echo $this->Form->input('', array(
											'type'=> 'select',
											'id' => 'version',
											'required' => true,
											'rel' => 'no-history',
											'empty' => __('Select', true),
											'options' => $version_number,
											'label' => false,
											'class' => 'version',
											'div' => false,
											'selected' => $versions['Version']['id'],
										));
									?>
								</div>
								<h2 class="version-title"><?php echo __('Z0Gravity version', true)?>: <span><?php echo $versions['Version']['name']?> </span></h2>
								<div class="wd-version">
									<h3 class="version-log-title"><?php echo __('Change logs', true)?></h3>
									<div class="wd-version-content">
									
										<?php echo nl2br($versions['Version']['content']); ?>
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

<script>
    $("#reset_form_1").click(function(){
        $("#title_form_update_version").html("<?php __("Add a new version") ?>");
        $("#VersionId").val("");
        $("#VersionName").val("");
        $("#VersionContent").val("");
        $("#flashMessage").hide();
        $(".error-message").hide();
                                                
        $("div.wd-input input, select").removeClass("form-error");
    });  
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });
    function editVersion(version_id,version_name){
        $("#VersionId").val(version_id);
        $("#VersionName").val(version_name);
        $("#VersionContent").val($('#content-'+version_id).text());
        $("#title_form_update_version").html("<?php __("Edit this version") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }  
    $('.wd-update').click(function(){
        var id = $(this).attr('id');
         $.post("/versions/update_version/"+ $(this).attr('id'),function(data,status){
            $('.wd-update').removeClass('wd-update-default');
            $('#'+id).addClass('wd-update-default');
             $('.update-version-default').text($('#version-name-'+id).text());
          });
    });
	$('#version').on('change', function(){
		_this = $(this);
		id = _this.val();
		_this.closest('.wd-content').addClass('loading');
		$.ajax({
			url: '<?php echo $this->Html->url('/') ?>versions/index/'+id,
			dataType: 'json',
			type: 'POST',
			success: function(data){
				if(data.data){
					versions = data.data.Version;
					$('.version-title span').html(versions.name);
					$('.wd-version .wd-version-content').html((versions.content).replace(/\n/g, "<br />"));
					_this.closest('.wd-content').removeClass('loading')
				}
			}
		});
	});
</script>