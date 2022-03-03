<style>
.ui-state-hover {
    border: 1px solid #004483;
    }
.new1, .ok-new-project{
    background-color: #217FC2 !important;
    font-size: 15px;
	color: #fff;
	text-transform: uppercase;
}
.cancel1, .cancel-new-project{
    background-color: #C6CCCF !important;
    font-size: 15px;
	color: #fff;
	text-transform: uppercase;
}
#dialog_duplicate.buttons ul.type_buttons{
	text-align: center;
}
#dialog_duplicate.buttons ul.type_buttons li{
	float: none;
	display: inline-block;
	vertical-align: middle;
}
</style>
<?php
App::import("vendor", "str_utility");
$str_utility = new str_utility();
?>
<!-- dialog_duplicate -->
<div id="dialog_duplicate" title="Note" class="buttons" style="display: none;">
    <center><strong ><?php __('Do you want to duplicate a project from a template?') ?></strong></center>
    <div style="clear: both;"></div>
    <ul class="type_buttons">
        <li><a href="javascript:void(0)" class="cancel-new-project" id="cancel"><?php __("No") ?></a></li>
        <li><a href="javascript:void(0)" class="ok-new-project" id="yes"><?php __('Yes') ?></a></li>
    </ul>
</div>
<!-- dialog_duplicate.end -->
<!-- dialog_projects -->
<div id="dialog_projects" title="<?php __('Project List') ?>" class="buttons" >

</div>
<!-- dialog_edit_task.end -->
<script type="text/javascript">
    $(document).ready(function(){
        $('#dialog_duplicate').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 300,
            height      :100
        });
        $('#dialog_projects').dialog({
            position    :'center',
            autoOpen    : false,
            autoHeight  : true,
            modal       : true,
            width       : 800,
            height      : 'auto'
        });

        $("#add_project").on('click',function(){
            $("#dialog_duplicate").dialog('open');
            $("#cancel").on('click',function(){
                window.location = '<?php echo $html->url('/projects/add') ?>';
            })
            $("#yes").on('click',function(){
                $("#dialog_duplicate").dialog('close');
                $("#dialog_projects").dialog('open');
                href = "<?php echo $html->url('/projects/projects_list/') ?>";
                $.ajax({
                    type: 'GET',
                    url : href,
                    beforeSend: function() {
                        $("#dialog_projects").html('<div align="center" style="color:black"><?php echo __("Loading...") ?><div><?php echo $html->image('ajax-loader.gif', array('align' => 'center', 'border' => '0')) ?></div></div>');
                    },
                    success : function(responseContent){
                        $('div#dialog_projects').html(responseContent);
                        $(".new1").on('click',function(){
                            var check = $("#table-list-admin ").find("input[name='data[Project][duplicate]']:checked").length;
                            if(check == 1){
                                $("#error span").text("");
                                $("#ProjectDuplicateForm").submit();
                            } else {
                                $("#error span").text("<?php __('Please select a project to copy'); ?>");
                            }
                        });
                        $(".cancel1").on('click',function(){
                            $("#dialog_projects").dialog('close');
                        });
                    }
                });
            });
        });
    });
</script>
