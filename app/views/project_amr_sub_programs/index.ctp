<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style type="text/css">
    .wd-project-admin fieldset div.wd-input label{
        width: 150px;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                                <!--<h2 class="wd-t1"><?php echo __("Project Statuses ", true); ?></h2> -->
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
                                <h2 class="wd-t3"></h2>

                                <form action="" id="table-list-admin-form" style="height: 200px; overflow: auto !important;">
                                    <table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                        <thead>
                                            <tr class="wd-header">
                                            <th class="wd-order" width="5%"><?php echo __('#', true); ?></th>
                                            <th class="wd-left"><?php echo __('Sub Program/Portfolio', true); ?></th>
                                            <th class="wd-left"><?php echo __('Program/Portfolio', true); ?></th>
                                            <?php if(!empty($companyConfigs['activate_family_linked_program'])):?>
                                            <th class="wd-left"><?php echo __('Sub Family', true); ?></th>
                                            <?php endif;?>
                                            <th class="wd-left"><?php echo __('Company', true); ?></th>
                                            <th><?php echo __("Actions", true); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            if (empty($projectAmrSubPrograms)) {
                                                ?>
                                                <tr>
                                                <td colspan="5"><center>No data available</center></td>
                                                </tr>
                                                <?php
                                            } else {
                                                foreach ($projectAmrSubPrograms as $projectAmrSubProgram):
                                                    $class = null;
                                                    $i++;
                                                    ?>
                                                    <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td id="sub-name-<?php echo $projectAmrSubProgram['ProjectAmrSubProgram']['id']; ?>"><?php echo $projectAmrSubProgram['ProjectAmrSubProgram']['amr_sub_program']; ?></td>
                                                    <td><?php echo !empty($projectPrograms[$projectAmrSubProgram['ProjectAmrSubProgram']['project_amr_program_id']]) ? $projectPrograms[$projectAmrSubProgram['ProjectAmrSubProgram']['project_amr_program_id']] : '';?></td>
                                                    <?php if(!empty($companyConfigs['activate_family_linked_program'])):?>
                                                    <td><?php echo !empty($famLists[$projectAmrSubProgram['ProjectAmrSubProgram']['sub_family_id']]) ? $famLists[$projectAmrSubProgram['ProjectAmrSubProgram']['sub_family_id']] : '';?></td>
                                                    <?php endif;?>
                                                    <td>
                                                    <?php
                                                        $com = !empty($companyPrograms[$projectAmrSubProgram['ProjectAmrSubProgram']['project_amr_program_id']]) ? $companyPrograms[$projectAmrSubProgram['ProjectAmrSubProgram']['project_amr_program_id']] : 0;
                                                        echo !empty($company_names[$com]) ? $company_names[$com] : '';
                                                    ?>
                                                    </td>
                                                    <td class="wd-action" nowrap >
                                                        <a class="wd-edit" title="Edit" href="javascript:void(0)"
                                                           onclick="editContent('<?php echo $projectAmrSubProgram['ProjectAmrSubProgram']['id']; ?>',
                                                               '<?php echo $projectAmrSubProgram['ProjectAmrSubProgram']['project_amr_program_id']; ?>',
                                                               '<?php echo $projectAmrSubProgram['ProjectAmrSubProgram']['sub_family_id']; ?>');">,
                                                            <?php __('Edit') ?></a>
                                                        <div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $projectAmrSubProgram['ProjectAmrSubProgram']['id']), array('class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $projectAmrSubProgram['ProjectAmrSubProgram']['amr_sub_program'])); ?>
                                                        </div>
                                                    </td>
                                                    </tr>
                                                    <?php
                                                endforeach;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </form>

                                <div class="wd-add-employee"> </div>

                                <?php echo $this->Form->create('ProjectAmrSubProgram', array("action" => "edit", 'id' => 'ProjectAmrSubProgramEditForm')); ?>
                                <?php echo $validation->bind("ProjectAmrSubProgram", array('form' => '#ProjectAmrSubProgramEditForm')); ?>
                                <h2 class="wd-title" id="title_form_update_city"><?php __("Add a new AMR sub program") ?></h2>
                                <?php echo $this->Session->flash(); ?>
                                <fieldset>
                                    <div class="wd-scroll-form-min" >
                                        <div class="wd-left-content">
                                            <div class="wd-input ">
                                                <label for="last-name"><?php __("Sub Program/Portfolio") ?></label>
                                                <?php
                                                echo $this->Form->input('amr_sub_program', array('name' => 'data[ProjectAmrSubProgram][amr_sub_program]',
                                                    'type' => 'text',
                                                    'div' => false,
                                                    'label' => false,
                                                    'style' => ' width:150px ',
                                                    "class" => "placeholder",
                                                    "placeholder" => __("Sub Program/Portfolio (*)", true)));
                                                ?>

                                            </div>
                                            <div class="wd-input">
                                                <label for="last-name"><?php __("Program/Portfolio") ?></label>
                                                <?php
                                                echo $this->Form->input('project_amr_program_id', array('name' => 'data[ProjectAmrSubProgram][project_amr_program_id]',
                                                    'type' => 'select',
                                                    'div' => false,
                                                    'label' => false,
                                                    "style" => "width:150px; float:none",
                                                    "empty" => "AMR Program (*)",
                                                    'onchange' => "listSubFamily(" . true .", " . 0 . ");",
                                                    "options" => $projectPrograms));
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

                                                echo $this->Form->input('company_id', array('name' => 'data[ProjectAmrSubProgram][company_id]',
                                                    'type' => 'select',
                                                    'div' => false,
                                                    'label' => false,
                                                    'default' => $employee_info['Company']['id'],
                                                    "options" => $company_names
                                                ));
                                                ?>
                                            </div>
                                            <?php if(!empty($companyConfigs['activate_family_linked_program'])):?>
                                            <div class="wd-input">
                                                <label for="last-name"><?php __("Sub Family") ?></label>
                                                <?php
                                                echo $this->Form->input('sub_family_id', array('name' => 'data[ProjectAmrSubProgram][sub_family_id]',
                                                    'type' => 'select',
                                                    'div' => false,
                                                    'label' => false,
                                                    //"style" => "width:150px; float:none",
                                                    "empty" => __("-- Select -- ", true),
                                                    "options" => $subFamilies));
                                                ?>
                                            </div>
                                            <?php endif;?>
                                        </div>
                                        <?php echo $this->Form->input('id', array('name' => 'data[ProjectAmrSubProgram][id]', 'type' => 'hidden')); ?>
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
    var familyIds = <?php echo !empty($familyIds) ? json_encode($familyIds) : '';?>;
    // get sub family
    function listSubFamily(check, subId){
        var programs = '';
        $('#ProjectAmrSubProgramProjectAmrProgramId option').each(function(){
            if($(this).is(':selected')){
                programs = $('#ProjectAmrSubProgramProjectAmrProgramId').val();
            }
        });
        var familyId = familyIds[programs] ? familyIds[programs] : '';
        var company_id = $('#ProjectAmrSubProgramCompanyId').val();
        if(familyId != ''){
            $.ajax({
                url: '/projects/getSubFamily/' + familyId + '/' + subId + '/' + company_id,
                async: true,
                beforeSend: function(){

                },
                success:function(datas) {
                    var datas = JSON.parse(datas);
                    $('#ProjectAmrSubProgramSubFamilyId').html(datas.select);
                    if(check == true){
                        $("#ProjectAmrSubProgramSubFamilyId").val(subId);
                    }
                }
            });
        } else {
            $('#ProjectAmrSubProgramSubFamilyId').html('<option value>-- Select --</option>');
        }
    }
    oTable = $('#table-list-admin_wrapper').dataTable({
        "sScrollY": "200px",
        "sDom": 'R<"H"lfr>t<"F"ip>',
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
    });

    function reset_form() {
        $("#ProjectAmrSubProgramAmrSubProgram").val('');
        $("#ProjectAmrSubProgramProjectAmrProgramId").val('');
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
        var project_sub_programe = $("#ProjectAmrSubProgramProjectAmrProgramId").val();
        if(project_sub_programe ==''){
            alert("You need to create AMR program before creating the AMR sub program");
        }else{
            $("#ProjectAmrSubProgramEditForm").submit();
        }
    })
    function editContent(project_city_id, a3, sub_family_id){
        var project_city_name = $('#sub-name-' + project_city_id).text();
        $("#ProjectAmrSubProgramId").val(project_city_id);
        $("#ProjectAmrSubProgramAmrSubProgram").val(project_city_name);
        ChuyenCompany(a3, sub_family_id);
        $("#title_form_update_city").html("<?php __("Edit the AMR sub program") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $("#ProjectAmrSubProgramId").val("");
        $("#ProjectAmrSubProgramCompanyId").val("");
        $("#ProjectAmrSubProgramProjectAmrProgramId").val("");
        $("#title_form_update_city").html("<?php __("Add a new AMR sub program") ?>");
        $("#ProjectAmrSubProgramAmrSubProgram").val("");
        $("#ProjectAmrSubProgramProjectAmrProgramId").val("");
        $.ajax({
            url: '<?php echo $html->url('/project_amr_sub_programs/get_amr/') ?>' + $("#ProjectAmrSubProgramCompanyId").val(),
            beforeSend: function() { $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option>Loading...</option>"); },
            success: function(data) {
                $("#ProjectAmrSubProgramProjectAmrProgramId").html(data);
            }
        });
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    });
    tam=0;
    function ChuyenCompany(amr_id, sub_family_id){

        if(amr_id==""){
            $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option></option>");
        }
        else{
            $.ajax({
                url: '<?php echo $html->url('/project_amr_sub_programs/get_2amr/') ?>' + amr_id,
                success: function(data) {
                    $("#ProjectAmrSubProgramCompanyId").val(data);
                    $.ajax({
                        url: '<?php echo $html->url('/project_amr_sub_programs/get_amr/') ?>' + data,
                        beforeSend: function() { $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option>Loading...</option>"); },
                        success: function(data) {
                            $("#ProjectAmrSubProgramProjectAmrProgramId").html(data);
                            $("#ProjectAmrSubProgramProjectAmrProgramId").val(amr_id);
                            listSubFamily(true, sub_family_id);
                        }
                    });
                }
            });

        }
    }
    $(document).ready(function () {
        var company_id = $("#ProjectAmrSubProgramCompanyId").val();

        if(company_id==""){
            $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option></option>");
        }
        else{
            $.ajax({
                url: '<?php echo $html->url('/project_amr_sub_programs/get_amr/') ?>' + company_id,
                beforeSend: function() { $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectAmrSubProgramProjectAmrProgramId").html(data);
                    listSubFamily();
                }
            });
        }
    });
    $("#ProjectAmrSubProgramCompanyId").change(function(){
        var company_id = $("#ProjectAmrSubProgramCompanyId").val();

        if(company_id==""){
            $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option></option>");
        }
        else{
            $.ajax({
                url: '<?php echo $html->url('/project_amr_sub_programs/get_amr/') ?>' + company_id,
                beforeSend: function() { $("#ProjectAmrSubProgramProjectAmrProgramId").html("<option>Loading...</option>"); },
                success: function(data) {
                    $("#ProjectAmrSubProgramProjectAmrProgramId").html(data);
                    listSubFamily();
                }
            });
        }
    });
</script>
