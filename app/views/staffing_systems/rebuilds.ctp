<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
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
				$TYPE = isset($this->params['pass'][0]) ? $this->params['pass'][0] : 'Activity' ;
				?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <div class="wd-content">
                                <h2 style="margin:5px 0 5px !important;" class="wd-t3"><?php __('Format Staffing') ?></h2>
                                <div id="divResults">
                                	<img src='<?php echo $this->Html->webroot('img/loader.gif'); ?>' alt='Loading' />
                                </div>
                                <div class="wd-title" style="margin-top:10px;">
                                <a onclick="rebuild();" style="float:left; display:none" class="wd-add-project" id="next_button" href="javascript:;">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Next Step') ?></span>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
#divResults{
	padding:20px 0;
	*border:1px solid #ccc;
	overflow:auto;
	height:260px;
	font-weight:bold;
	font-size:14px;
	word-spacing:13px;
	color:#013d74;
	line-height:22px;
}
.finish{
	font-size:24px;
	color:#06F;
}
</style>

<script>
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/loader.gif'); ?>' alt='Loading' /></span>";
var flag = setTimeout(function(){
	rebuild();
},1000);
function rebuild(){
	$('#divResults').html(loading);
	$('#next_button').hide();
	$.ajax({
		url  : "/staffing_systems/format/<?php echo $TYPE;?>",
		type : "POST",
		data : {},
		success : function(data){
			if(data)
			{
				data = JSON.parse(data);
				var html = data.html;
				var next = data.next;
				if(next == 1)
				{
					$('#next_button').show();
				}
				else
				{
					html = '<span class="finish"><?php __('Finish!!!!') ?></span>';
				}
				$('#divResults').html(html);

			}
		}
	});
}
</script>
