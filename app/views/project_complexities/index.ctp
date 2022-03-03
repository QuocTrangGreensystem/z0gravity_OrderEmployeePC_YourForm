<style type="text/css">
#wd-container-footer{
    display: none;
}
</style>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
if (($is_sas == 1) || ($employee_info["Role"]["name"] == "admin")) {
    ?>
    <div id="wd-container-main" class="wd-project-admin">
        <div class="wd-layout">

            <div class="wd-main-content">
                <div class="wd-list-project">
                    <div class="wd-title">
                    </div>
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    //debug()
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
                                                <th class="wd-order" width="5%"><?php echo $paginator->sort(__('#', true), 'id'); ?></th>
                                                <th class="wd-left"><?php echo $paginator->sort(__('Name', true), 'name'); ?></th>
                                                <?php
                                                if (($is_sas == 1) || (($employee_info["Role"]["name"] == "admin") && ($employee_info["Company"]["parent_id"] == ""))) {
                                                    ?>
                                                    <th> <?php echo $paginator->sort(__("Company", true), "Company.company_name"); ?> </th>
                                                    <?php
                                                }
                                                ?>
                                                <th width="8%"><?php __('Display'); ?></th>
                                                <th><?php echo __("Actions", true); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 0;
                                                if (empty($projectComplexities)) {
                                                    ?>
                                                    <tr>
                                                    <td colspan="4"><center>No data available</center></td>
                                                    </tr>
                                                    <?php
                                                } else {
                                                    foreach ($projectComplexities as $projectComplexity):
                                                        $class = null;
                                                        $i++;
                                                        if ($projectComplexity['ProjectComplexity']['company_id'] != "") {
                                                            $company_id_save = $projectComplexity['ProjectComplexity']['company_id'];
                                                            $company_name = $companies[$company_id_save];
                                                            if ($parent_companies[$company_id_save] != "") {
                                                                $company_name = $companies[$parent_companies[$company_id_save]] . " --> " . $company_name;
                                                            }
                                                        }
                                                        else
                                                            $company_name = " ";
                                                        ?>
                                                        <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td id="name-<?php echo $projectComplexity['ProjectComplexity']['id']; ?>"><strong><?php echo $projectComplexity['ProjectComplexity']['name']; ?></td>
                                                        <?php
                                                        if (($is_sas == 1) || (($employee_info["Role"]["name"] == "admin") && ($employee_info["Company"]["parent_id"] == ""))) {
                                                            ?>
                                                            <td><?php echo __($company_name); ?></td>
                                                            <?php
                                                        }
                                                        ?>
                                                        <td>
                                                            <?php
                                                                if($projectComplexity['ProjectComplexity']['display']){
                                                                    echo __("Yes", true);
                                                                }else{
                                                                    echo __("No", true);
                                                                }
                                                            ?>
                                                        </td>
                                                        <td class="wd-action" nowrap >
                                                            <a class="wd-edit" title="Edit" href="javascript:void(0)"
                                                               onclick="editCity('<?php echo $projectComplexity['ProjectComplexity']['id']; ?>',
                                                                   '<?php echo $projectComplexity['ProjectComplexity']['company_id']; ?>',
                                                                   '<?php echo $projectComplexity['ProjectComplexity']['display']; ?>');">
                                                                <?php __('Edit') ?></a>
                                                            <div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $projectComplexity['ProjectComplexity']['id']), array('class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $projectComplexity['ProjectComplexity']['name'])); ?>
                                                            </div>
                                                        </td>
                                                        </tr>
                                                        <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <?php /*     <div class="wd-paging">
                                          <?php echo $paginator->prev(__("Prev", true), array('class'=>'wd-prev', 'tag' => 'span'), __("Prev", true), array('class'=>'wd-prev wd-disable', 'tag' => 'span'));?>
                                          <?php echo $paginator->numbers(array("separator" => " ")); ?>
                                          <?php echo $paginator->next(__("Next", true), array('class' => 'wd-next', 'tag' => 'span'), __("Next", true), array('class'=>'wd-next wd-disable', 'tag' => 'span'));?>

                                          </div> */ ?>
                                    </form>

                                    <div class="wd-add-employee"></div>
                                    <?php echo $this->Form->create('ProjectComplexity', array("action" => "edit", 'id' => 'ProjectComplexityEditForm')); ?>
                                    <?php echo $validation->bind("ProjectComplexity", array('form' => '#ProjectComplexityEditForm')); ?>
                                    <h2 class="wd-title" id="title_form_update_city"><?php __("Add a new implementation complexity") ?></h2>
                                    <?php echo $this->Session->flash(); ?>
                                    <fieldset>
                                        <div class="wd-scroll-form-min">
                                            <div class="wd-left-content">
                                                <div class="wd-input">
                                                    <label for="last-name"><?php __("Name") ?></label>
                                                    <?php
                                                    echo $this->Form->input('name', array('name' => 'data[ProjectComplexity][name]',
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'label' => false,
                                                        "class" => "placeholder", "placeholder" => __("Name (*)", true)));
                                                    ?>
                                                </div>
                                                <div class="wd-input ">
                                                    <label for="display"><?php __("Display") ?></label>
                                                    <?php
                                                    echo $this->Form->input('display', array('name' => 'data[ProjectComplexity][display]',
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

                                                    if (($is_sas == 1) || (($employee_info["Role"]["name"] == "admin") && ($employee_info["Company"]["parent_id"] == ""))) {
                                                        echo $this->Form->input('company_id', array('name' => 'data[ProjectComplexity][company_id]',
                                                            'type' => 'select',
                                                            'div' => false,
                                                            'label' => false,
                                                            'default' => $employee_info['Company']['id'],
                                                            "empty" => __("-- Select -- ", true),
                                                            "options" => $company_names));
                                                    } else {
                                                        ?>
                                                        <p style="padding-top: 6px;"><?php __($employee_info["Company"]["company_name"]) ?></p>
                                                        <?php
                                                        echo $this->Form->input('company_id', array('type' => 'hidden', 'value' => $employee_info["Company"]["id"]));
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php echo $this->Form->input('id', array('name' => 'data[ProjectComplexity][id]', 'type' => 'hidden')); ?>
                                        </div>
                                        <div class="wd-submit">
                                            <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                            <?php
                                            if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"]))))
                                                $reset = 'reset_form_1'; else
                                                $reset = 'reset_form';
                                            ?>
                                            <a href="javascript:void(0);" id="<?php echo $reset ?>" class="wd-reset"><?php __('Reset') ?></a>
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
<?php } else { ?>
    <div align="center">
        <br/>
        <strong>You do not have permission to access.</strong>
        <br/>
        <br/>
    </div>
    <?php
}
?>
<script>

    /*oTable = $('#table-list-admin_wrapper').dataTable( {
                "sScrollY": "150px",
                "sDom": 'R<"H"lfr>t<"F"ip>',
                "bJQueryUI": true,
                "sPaginationType": "full_numbers"//,
                //"sDom": 'lfrtip'
        } ); */
<?php if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"])))) { ?>
        $("#reset_form_1").click(function(){
            $("#ProjectStatusId").val("");
            $("input[id='ProjectStatusName']").val('');
            $("#title_form_update_city").html("<?php __("Add a new implementation complexity") ?>");
            $("#ProjectStatusIdName").val("");
            $("#flashMessage").hide();
            $(".error-message").hide();
            $("div.wd-input input, select").removeClass("form-error");

        });
<?php } ?>
    function reset_form() {

    }


    function editCity(project_city_id, company_id, display){
        var project_city_name = $('#name-' + project_city_id).text();
        $("#ProjectComplexityId").val(project_city_id);
        $("#ProjectComplexityName").val(project_city_name);
        $("#ProjectComplexityCompanyId").val(company_id);
        $('#ProjectComplexityDisplay').val(display);
        //alert(project_city_id + "  - " + project_city_name + " -- " +  company_id);
        $("#title_form_update_city").html("<?php __("Edit this implementation complexity") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $("#ProjectComplexityId").val("");
        $("input[id='ProjectComplexityName']").val('');
        $("#ProjectComplexityCompanyId").val('');
        $("#title_form_update_city").html("<?php __("Add a new implementation complexity") ?>");
        $("#ProjectStatusIdName").val("");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input input, select").removeClass("form-error");

    });
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
        $("div.wd-input input, select").removeClass("form-error");
    });

</script>
