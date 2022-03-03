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
<style>
.inputConfig{
	width:240px;
	padding:5px ;
	border:1px solid #CCC !important;
}
select.inputConfig{
	width:252px;
	background:none !important;
}
.inputConfig:focus{
	background: url('/img/edit.png') 230px 5px  no-repeat ;
}
.wd-input-select label{display:inline-block;min-width:175px; font-weight:bold;}
.wd-input-select{ margin:5px 0; }
.section_cf{ display:none }
#loadingElm{
	color:#00F; font-size:18px; margin-left:10px;
}
#loadingElm img{
	margin-top:5px;
}
.wd-save{
	background: url(<?php echo $this->Html->webroot('img/front/bg-submit-save.png'); ?>) no-repeat left top;
	cursor: pointer;
	height: 33px;
	width: 82px;
	border: none;
	font-size: 0;
	margin-top: 10px;
}
.wd-list-project .wd-tab .wd-content label {
	margin-top: 7px;
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
                        	<?php echo $this->element('administrator_left_menu');
							$option = $tree;
							?>
                            <div class="wd-content" style="padding-top:20px;">
                                
                                <!--SECTION Upload Multiple Servers-->
                                <div id="section_upload_multiple_server" class="section_cf" style="display:inline-block">
                                	<h2 class="wd-t3"><?php __('Clone data for new company') ?></h2>
                                    <div class="wd-input-select clearfix">
                                            <label><?php echo __("Standard Company", true);?></label>
                                            <?php
                                                echo $this->Form->input('standard_company', array(
                                                    'div' => false, 
                                                    'label' => false,
													"default" => $standard_company,
                                                    "class" => "inputConfig",
													"options" => $option
                                                    ));
                                            ?>
                                        </div>
                                         <div class="wd-input-select clearfix">
                                            <label><?php echo __("New Company", true);?></label>
                                            <?php
                                                echo $this->Form->input('new_company', array(
                                                    'div' => false, 
                                                    'label' => false,
													"default" => $new_company,
                                                    "class" => "inputConfig",
													"options" => $option
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select clearfix">
                                            <label></label>
                                            <input type="button" onclick="confirmClone();" id="btnSave" value="" class="wd-save"/>
                                        </div>
                                </div>
                                <!--END Upload Multiple Servers-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
var result  = '<div id="cloneResult"></div>';
function confirmClone(field,value)
{
	if( $(field).attr('disabled') ) return false;
	var standard_company = $('#standard_company').val();
	var new_company = $('#new_company').val();
	if(standard_company != '' && new_company != '')
	{
		if(!confirm('Are you sure ?'))
		return false; 
		$('#cloneResult').remove();
		$(loading).insertAfter('#btnSave'); 
		$('#btnSave').prop('disabled', true);
		$.ajax({
			url: '/installs/cloneDataForNewCompany/'+standard_company+'/'+new_company,
			type:'POST',
			success:function(data) {
				$('#loadingElm').remove();
				$(result).insertAfter('#btnSave'); 
				$('#cloneResult').html(data);
				$('#btnSave').removeAttr('disabled');
			}
		});	
	}	
}
</script>