<?php
    echo $this->Html->css(array(
		'projects',
		'activity_request',
		'slick_grid/slick.pager',
		'slick_grid/slick.common',
		'slick_grid/slick.edit'
	));
    $idHtml = !empty($reports) && !empty($reports['Report']['id']) ? $reports['Report']['id'] : '';
?>
<style>
#table-control .input{display:block; padding-left:0}.export-excel-icon-all{float: left;}#table-control{float:left;}
.ch-button-delete{
    background-attachment: scroll;
    background-clip: border-box;
    background-color: transparent;
    background-image: url("/img/front/bg-add-project.png");
    background-origin: padding-box;
    background-position: left top;
    background-repeat: no-repeat;
    background-size: auto auto;
    color: #FFFFFF;
    display: block;
    float: right;
    height: 33px;
    line-height: 33px;
    padding-left: 27px;
    text-decoration: none;
}
.ch-button-delete span{
    background-attachment: scroll;
    background-clip: border-box;
    background-color: transparent;
    background-image: url("/img/front/bg-add-project-right.png");
    background-origin: padding-box;
    background-position: right top;
    background-repeat: no-repeat;
    background-size: auto auto;
    display: block;
    height: 33px;
    padding-bottom: 0;
    padding-left: 2px;
    padding-right: 15px;
    padding-top: 0;
    font-size: 13px;
    text-decoration: none;
    color: #FFFFFF;
}
.ch-button-delete:hover span {
    background-position: right -33px;
    text-decoration: none;
}
.ch-button-delete:hover {
    background-position: left -33px;
    text-decoration: none;
}
.ch-button-delete{
    background-image: url("/img/front/bg-delete-project.png") !important;
}
.ch-button-delete span{
    background-image: url("/img/front/bg-delete.png") !important;
}
</style>
<!-- dialog_add_html -->
<div id="dialog_add_html" style="display:none" title="Add Email" class="buttons">
    <?php
    echo $this->Form->create('Report', array('id' => 'addHtmlForm', 'type' => 'file',
        'url' => array('controller' => 'reports', 'action' => 'update', $type, $company_id, $idHtml)));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('Email:') ?></label>
            <?php 
            echo $this->Form->input('data', array('type' => 'text', 
                'div' => false, 'label' => false,
                'id' => 'textareaData',
                "style" => "width: 97%; font-size: 14px;",
                'value' => !empty($reports) && !empty($reports['Report']['data']) ? '' : '' 
                )); ?>
        </center>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="add_html_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- dialog_add_html -->

<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title" style="margin-bottom: 3px !important; padding-top: 5px; margin-top: -12px;">
                   <h2 class="wd-t1"><?php echo __("Absences Dashboard", true); ?></h2>
                   <?php
                   if($isAdmin):
                        if(!empty($idHtml)):
                   ?>
                        <a onclick="return confirm('<?php echo __('Are you sure you want to delete?', true); ?>');" href="<?php echo $this->Html->url(array('action' => 'delete', $idHtml));?>" class="ch-button-delete" style="margin-left: 6px;" ><span><?php __('Delete Email') ?></span></a>
                   <?php endif;?>
                   <a href="javascript:void(0);" id="add_html" class="wd-add-project"><span><?php __('Add Email') ?></span></a>
                   <?php endif;?>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%;height:100%; border: 1px solid rgb(228, 228, 228)">
                    <?php
                        if(!empty($reports) && !empty($reports['Report']['data'])){
							$url = $this->Tableau->get_trusted_url($reports['Report']['data'],'178.33.41.138','t/azuree/views/POCAzure-Absences/Tableaudebord');
							if ($url==0) {
								echo '<p style="width:400px;height:400px;font-size: 16px; color: red; padding: 6px;">Report not exist!</p>';
							} else if ($url==-2){
								echo '<p style="width:400px;height:400px;font-size: 16px; color: red; padding: 6px;">Pecl_http library not found!</p>';
							} else {
                            //echo $reports['Report']['data'];
					?>
							<iframe src="<?php echo $url;?>" width="1353" height="563"></iframe>
					<?php
							}
                        } else {
                            echo '<p style="width:400px;height:400px;font-size: 16px; color: red; padding: 6px;">Report not exist!</p>';
                        }
                    ?>
                </div>
                <!--div id="pager" style="width:100%;height:36px; overflow: hidden;">

                </div-->
            </div>
            <?php //echo $this->element('grid_status'); ?>
        </div>
    </div>
</div>
<script>
    $(window).resize(function() {
        var lHeight =  $(window).height();
		var DialogFullHeight = Math.round((45*lHeight)/100);
		var lWidth = $(window).width();
		var DialogFull = Math.round((30*lWidth)/100);
        var heightArea = Math.round((70*DialogFullHeight)/100);
        DialogFullHeight = (DialogFullHeight <= 250) ? 250 : DialogFullHeight;
        heightArea = (heightArea <= 150) ? 150 : heightArea;
        //$('#textareaData').height(heightArea);
        $('#dialog_add_html').parent().css({
            'width': DialogFull + 'px',
            //'height': DialogFullHeight + 'px',
        });
    });
    $('#add_html').click(function(){
        var lHeight =  $(window).height();
		var DialogFullHeight = Math.round((45*lHeight)/100);
		var lWidth = $(window).width();
		var DialogFull = Math.round((30*lWidth)/100);
        var heightArea = Math.round((70*DialogFullHeight)/100);
        DialogFullHeight = (DialogFullHeight <= 250) ? 250 : DialogFullHeight;
        heightArea = (heightArea <= 150) ? 150 : heightArea;
        //$('#textareaData').height(heightArea);
		$('#dialog_add_html').dialog({
			position    :'center',
			autoOpen    : false,
			autoHeight  : true,
			modal       : true,
			width       : DialogFull,
            //height      : DialogFullHeight,
			open : function(e){
				var $dialog = $(e.target);
				$dialog.dialog({open: $.noop});
			}
		});
		$("#dialog_add_html").dialog('option',{title:'Add Email'}).dialog('open');
    });
    $(".cancel").live('click',function(){
        $("#dialog_add_html").dialog("close");
	});
    $('#add_html_submit').click(function(){
        var _data = $('#textareaData').val();
        if(_data){
            $("#dialog_add_html").dialog("close");
            $('#addHtmlForm').submit();
        } else {
            return false;
        }
    });
</script>