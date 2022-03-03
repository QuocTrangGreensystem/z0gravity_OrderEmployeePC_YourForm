<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
?>
<style>
#ProjectSubTypeEditForm{
	top: 15px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                                <!--<h2 class="wd-t1"><?php echo __("Project AMR Programs ", true); ?></h2> -->
                </div>
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
                                <div class="projectSubTypes index">
                                    <h2 class="wd-t3"></h2>
                                    <form action="" id="table-list-admin-form" style="height: 200px; overflow: auto !important;">
                                        <table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                            <thead>
                                                <tr class="wd-header">
                                                <th class="wd-order" width="5%"><?php echo __('#', true); ?></th>
                                                <th><?php echo __('Sub type', true); ?></th>
                                                <th class="wd-left"><?php echo __('Type', true); ?></th>
                                                <th class="wd-left"><?php echo __('Company', true); ?></th>
                                                <th width="8%"><?php __('Display'); ?></th>
                                                <th class="actions"><?php __('Actions'); ?></th>
                                                </tr>
                                            </thead>
                                            <?php
                                            $i = 0;
                                            if (!empty($projectSubTypes)) {
                                                foreach ($projectSubTypes as $projectSubType):
                                                    $class = null;
                                                    if ($i++ % 2 == 0) {
                                                        $class = ' class="altrow"';
                                                    }
                                                    ?>
                                                    <tr<?php echo $class; ?>>
                                                    <td><?php echo $i; ?>&nbsp;</td>
                                                    <td><?php echo $projectSubType['ProjectSubType']['project_sub_type']; ?>&nbsp;</td>
                                                    <td><?php echo $projectSubType['ProjectType']['project_type']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($projectSubType['ProjectType']['Company'])) {
                                                            echo $projectSubType['ProjectType']['Company']['company_name'];
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            if($projectSubType['ProjectSubType']['display']){
                                                                echo __("Yes", true);
                                                            }else{
                                                                echo __("No", true);
                                                            } 
                                                        ?>  
                                                    </td>
                                                    <td class="wd-action" nowrap="nowrap" > 
                                                        <a class="wd-edit" title="Edit" href="javascript:void(0)" 
                                                           onclick="editContent('<?php echo $projectSubType['ProjectSubType']['id']; ?>',
                                                               '<?php echo $projectSubType['ProjectSubType']['project_sub_type']; ?>',
                                                               '<?php echo $projectSubType['ProjectSubType']['project_type_id']; ?>',
                                                               '<?php echo $projectSubType['ProjectSubType']['display']; ?>');">
                                                               <?php __('Edit') ?>
                                                        </a>
                                                        <div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $projectSubType['ProjectSubType']['id']), array('class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $projectSubType['ProjectSubType']['project_sub_type'])); ?>
                                                        </div>
                                                    </td>
                                                    </tr>
                                                    <?php
                                                endforeach;
                                            }
                                            ?>
                                        </table>
                                    </form>
                                    <?php echo $this->Form->create('ProjectSubType', array("action" => "edit", 'id' => 'ProjectSubTypeEditForm')); ?>
                                    <?php echo $validation->bind("ProjectSubType", array('form' => '#ProjectSubTypeEditForm')); ?>
                                    <h2 class="wd-title" id="title_form_update_city"><?php __("Add a new sub type") ?></h2>
                                    <?php echo $this->Session->flash(); ?>
                                    <fieldset>
                                        <div class="wd-scroll-form-min" >
                                            <div class="wd-left-content">
                                                <div class="wd-input ">
                                                    <label for="last-name"><?php __("Sub Type") ?></label>	
                                                    <?php
                                                    echo $this->Form->input('project_sub_type', array('name' => 'data[ProjectSubType][project_sub_type]',
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'label' => false,
                                                        'style' => ' width:150px ',
                                                        "class" => "placeholder",
                                                        "placeholder" => '(*)'
                                                        ));
                                                    ?>

                                                </div>
                                                <div class="wd-input ">
                                                    <label for="display"><?php __("Display") ?></label>	
                                                    <?php
                                                    echo $this->Form->input('display', array('name' => 'data[ProjectSubType][display]',
                                                            'type' => 'select',
                                                            'div' => false,
                                                            'label' => false,
                                                            "empty" => false,
                                                            "style" =>"width: 20%",
                                                            "options" => array(1 => __('Yes', true), 0 => __('No', true))));
                                                    ?>
                                                </div>
                                            </div>	

                                            <div class="wd-right-content">
                                                <div class="wd-input">
                                                    <label for="last-name"><?php __("Company") ?></label>
                                                    <?php
                                                    if (!empty($employee_info['Company']))
                                                        $employee_info['Company']['id'] = $employee_info['Company']['id'];
                                                    else
                                                        $employee_info['Company']['id'] = "";
                                                    
                                                    echo $this->Form->input('company_id', array('name' => 'data[ProjectSubType][company_id]',
                                                        'type' => 'select',
                                                        'div' => false,
                                                        'label' => false,'default' => $employee_info['Company']['id'],
                                                        'default' => $employee_info['Company']['id'],
                                                        "options" => $company_names
                                                    ));
                                                    ?>
                                                </div>
                                                <div class="wd-input">
                                                    <label for="last-name"><?php __("Project Type") ?></label>
                                                    <?php
                                                    echo $this->Form->input('project_type_id', array('name' => 'data[ProjectSubType][project_type_id]',
                                                        'type' => 'select',
                                                        'div' => false,
                                                        'label' => false,
                                                        "style" => "width:150px; float:none",
                                                        "empty" => "Project type (*)",
                                                        "options" => $projectTypes));
                                                    ?>
                                                </div>
                                            </div>	
                                            <?php echo $this->Form->input('id', array('name' => 'data[ProjectSubType][id]', 'type' => 'hidden')); ?>
                                        </div>

                                        <div class="wd-submit">
                                            <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                            <a href="javascript:reset_form();" id="reset_form" class="wd-reset"><?php echo __("Reset") ?></a>
                                        </div>
                                    </fieldset>
                                    <?php echo $this->Form->end(); ?>
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
    oTable = $('#table-list-admin_wrapper').dataTable({
        "sScrollY": "200px",
        "sDom": 'R<"H"lfr>t<"F"ip>',
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
    });
    
    function reset_form() {
        $("#ProjectSubTypeProjectSubType").val('');
        $("#ProjectSubTypeProjectTypeId").val('');
        $("#ProjectSubTypeId").val('');
    }
    $(function(){
        var oTable;
        $("thead input").keyup( function () {
            oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex( 
            oTable.fnSettings(), $("thead input").index(this) ) );
        } );
    
        $("thead input").each( function (i) {
            this.initVal = this.value;
        } );
    
    });
    $('#btnSave').click(function(){
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
        var project_type = $("#ProjectSubTypeProjectTypeId").val();
        if(project_type ==''){
            alert("You need to create Project Type before creating the Project SubType");
        }else{
            $("#ProjectSubTypeEditForm").submit();
        }
		
    })
    function editContent(project_city_id, project_city_name,a3,display){
        $("#ProjectSubTypeId").val(project_city_id);
        $("#ProjectSubTypeProjectSubType").val(project_city_name);
        $("#ProjectSubTypeProjectTypeId").val(a3);
        $('#ProjectSubTypeDisplay').val(display);
        ChuyenCompany(a3);
        $("#title_form_update_city").html("<?php __("Edit the sub type") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $("#ProjectSubTypeProjectSubType").val('');
        $("#ProjectSubTypeProjectTypeId").val('');
        $("#ProjectSubTypeId").val('');
        $("#ProjectSubTypeCompanyId").val('');
        $.ajax({
            url: '<?php echo $html->url('/project_sub_types/get_type/') ?>' + $("#ProjectSubTypeCompanyId").val(),
            beforeSend: function() { $("#ProjectSubTypeProjectTypeId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectSubTypeProjectTypeId").html(data);
            } 
        });
        $("#ProjectSubTypeProjectTypeId").val('');
        $("#title_form_update_city").html("<?php __("Add a new sub type") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    });
    tam=0;
    function ChuyenCompany(amr_id){
		
        if(amr_id==""){
            $("#ProjectSubTypeProjectTypeId").html("<option></option>");
        }
        else{
            $.ajax({
                url: '<?php echo $html->url('/project_sub_types/get_2type/') ?>' + amr_id,
                success: function(data) {
                    $("#ProjectSubTypeCompanyId").val(data);
                    $.ajax({
                        url: '<?php echo $html->url('/project_sub_types/get_type/') ?>' + data,
                        beforeSend: function() { $("#ProjectSubTypeProjectTypeId").html("<option>Loading...</option>"); },
                        success: function(data) {
                            $("#ProjectSubTypeProjectTypeId").html(data);
                            $("#ProjectSubTypeProjectTypeId").val(amr_id);
                        } 
                    });
                } 
            });
			
        }
    }
    $(document).ready(function () {
        var company_id = $("#ProjectSubTypeCompanyId").val();
		
        if(company_id==""){
            $("#ProjectSubTypeProjectTypeId").html("<option></option>");
        }
        else{
            $.ajax({
                url: '<?php echo $html->url('/project_sub_types/get_type/') ?>' + company_id,
                beforeSend: function() { $("#ProjectSubTypeProjectTypeId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectSubTypeProjectTypeId").html(data);
                } 
            });
        }
    });
    $("#ProjectSubTypeCompanyId").change(function(){
        var company_id = $("#ProjectSubTypeCompanyId").val();
		
        if(company_id==""){
            $("#ProjectSubTypeProjectTypeId").html("<option></option>");
        }
        else{
            $.ajax({
                url: '<?php echo $html->url('/project_sub_types/get_type/') ?>' + company_id,
                beforeSend: function() { $("#ProjectSubTypeProjectTypeId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectSubTypeProjectTypeId").html(data);
                } 
            });
        }
    });
</script>