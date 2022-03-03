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
	float: none;
}
.inputConfig:focus{
	background: url('/img/edit.png') 230px 5px  no-repeat ;
}
.wd-input-select label{display:inline-block;min-width:175px; font-weight:bold;}
.wd-input-select{ margin:5px 0; }
.section_cf{ display:none }
.loadingElm img{
	margin-top:5px;
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
				foreach($systemConfigs as $key => $value)
				{
					$$key = $value;
				}
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                        	<div class="wd-aside-left">
                                <div id="accordion">
                                	<!--SECTION TITLE-->
                                	<h3 id="title_upload_multiple_server" onclick="showMe('section_upload_multiple_server');" class="head-title wd-current"><a href="javascript:;" ><?php __("Upload Multiple Servers"); ?></a></h3>
                                    <h3 id="title_new_section_id" style="display:none" onclick="showMe('new_section_id');" class="head-title"><a href="javascript:;" ><?php __("New Section"); ?></a></h3>
                                    <!--END-->
                                </div>
                            </div>
                            <div class="wd-content" style="padding-top:20px;">
                            	<!--SECTION Upload Multiple Servers-->
                            	<div id="new_section_id" class="section_cf">
                                	<!-- CONTENT NEW SECTION -->
                                    NEW CONTENT
                                    <!-- END-->
                                </div>
                                <!--END--> 
                                
                                <!--SECTION Upload Multiple Servers-->
                                <div id="section_upload_multiple_server" class="section_cf" style="display:block">
                                	<h2 class="wd-t3"><?php __('Upload Multiple Servers') ?></h2>
                                    <div class="wd-input-select">
                                            <label><?php echo __("Upload Multiple Servers", true);?></label>
                                            <?php
												$option = array('-1' => 'None', '0' => 'No', '1' => 'Yes');
                                                echo $this->Form->input('upload_multiple_server', array(
                                                    'div' => false, 
                                                    'label' => false,
													"default" => &$upload_multiple_server,
													'onchange' => "editMe(this.id,this.value);",
                                                    "class" => "inputConfig",
													"options" => $option
                                                    ));
                                            ?>
                                        </div>
                                         <div class="wd-input-select">
                                            <label><?php echo __("IP Address (1)", true)?></label>
                                            <?php
												
                                                echo $this->Form->input('upload_multiple_server_ip', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe(this.id,this.value);",
                                                    "class" => "inputConfig",
                                                    "default" => &$upload_multiple_server_ip,
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("IP Address (2)", true)?></label>
                                            <?php
												
                                                echo $this->Form->input('upload_multiple_server_ip_1', array(
                                                    'div' => false, 
                                                    'label' => false,
													'onchange' => "editMe(this.id,this.value);",
                                                    "class" => "inputConfig",
                                                    "default" => &$upload_multiple_server_ip_1,
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("User Name", true)?></label>
                                            
                                            <?php
												echo $this->Form->input('upload_multiple_server_user', array(
                                                    'div' => false, 
                                                    'label' => false,
													 "default" => &$upload_multiple_server_user,
													'onchange' => "editMe(this.id,this.value);",
                                                    "class" => "inputConfig"
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Password", true)?></label>
                                            
                                            <?php
												echo $this->Form->input('upload_multiple_server_pass', array(
                                                    'div' => false, 
													'type' => 'password',
                                                    'label' => false,
													 "default" => &$upload_multiple_server_pass,
													'onchange' => "editMe(this.id,this.value);",
                                                    "class" => "inputConfig"
                                                    ));
                                            ?>
                                            <input style="margin-top:4px;" type="checkbox" onchange="showPass('upload_multiple_server_pass')" value="0" name="showPass"> <strong><?php echo __("Show Password", true)?></strong>
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
function showPass(elm)
{
	var $input = $('#'+elm);
	var change = $input.attr("type")=='password' ? "text" : "password";
	var id = $input.attr("id");
	var name = $input.attr("name");
	var classElm = $input.attr('class');
	var onchange = $input.attr('onchange');
	var value = $input.val();
	var rep = $("<input type='" + change + "' id='" + id + "' name='" + name + "' class='" + classElm + "' value='" + value + "' onchange=\"onchange\" />").insertBefore($input);
	$input.remove();
	$input = rep;
	$input.attr('onchange',onchange);
}
function editMe(field,value)
{
	$(loading).insertAfter('#'+field);
	var data = field+'/'+value;
	$.ajax({
		url: '/system_configs/editMe/',
		data: {
			data : { value : value, field : field }
		},
		type:'POST',
		success:function(datas) {
			if(datas==1)
			{
				$('#'+field).addClass('ok');
			}
			else
			{
				$('#'+field).addClass('ko');
			}
			$('#loadingElm').remove();
		}
	});
}
function showMe(section)
{
	$('.section_cf').hide();
	$('#'+section).fadeIn();
	$('.head-title').removeClass('wd-current');
	$('#title_'+section).addClass('wd-current');
}
</script>