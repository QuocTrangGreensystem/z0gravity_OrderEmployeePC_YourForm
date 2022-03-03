<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->script('select2.min'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('select2.min'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style>
.select2-container .select2-selection--single{
    height: 35px;
    border: none;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height: 35px;
}
.select2-search--dropdown{
    display: none;
}
.select2-results__options{
    display: block;
    margin: 0 auto;
}
</style>
<?php
$employee_info = $this->Session->read("Auth.employee_info");
if (($is_sas == 1) || ($employee_info["Role"]["name"] == "admin")) {
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
                    ?>
                    <div class="wd-tab">
                        <?php
                        if($view != 'ajax'){
                            echo $this->element("admin_sub_top_menu");
                        }
                        ?>
                        <div class="wd-panel">
                            <div class="wd-section" id="wd-fragment-1">
                                <?php
                                if($view != 'ajax'){
                                    echo $this->element('administrator_left_menu');
                                }
                                ?>
                                <div class="wd-content">
                                    <h2 class="wd-t3"></h2>

                                    <form action="" id="table-list-admin-form" style="height: 200px; overflow: auto !important;">
                                        <table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
                                            <thead>
                                                <tr class="wd-header">
                                                <th class="wd-order" width="5%">
                                                    <?php
                                                    echo $paginator->sort(__('#', true), 'id');
                                                    ?>
                                                </th>
                                                <th class="wd-left"><?php echo $paginator->sort(__('Deliverable Category', true), 'livrable_cat'); ?></th>
                                                <th></th>
                                                <th><?php echo __("Actions", true); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 0;
                                                if (empty($projectLivrableCategories)) {
                                                    ?>
                                                    <tr>
                                                    <td colspan="4"><center>No data available</center></td>
                                                    </tr>
                                                    <?php
                                                } else {
                                                    foreach ($projectLivrableCategories as $projectLivrableCategory):
                                                        $class = null;
                                                        $i++;
                                                        if ($projectLivrableCategory['ProjectLivrableCategory']['company_id'] != "") {
                                                            $company_id_save = $projectLivrableCategory['ProjectLivrableCategory']['company_id'];
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
                                                        <td><strong><?php echo $projectLivrableCategory['ProjectLivrableCategory']['livrable_cat']; ?></td>
														<?php 
															//Ticket #1451
															// $value_encode = json_encode($projectLivrableCategory['ProjectLivrableCategory']['livrable_cat'], JSON_HEX_APOS);
															$value_encode = str_replace("'", "\'", $projectLivrableCategory['ProjectLivrableCategory']['livrable_cat']);
														?>
                                                        <td><?php
                                                        echo !empty($projectLivrableCategory['ProjectLivrableCategory']['livrable_icon']) ? '<img style="vertical-align: bottom;margin: 0 auto;display: block;" src="/img/new-icon/project_document/'.$projectLivrableCategory['ProjectLivrableCategory']['livrable_icon'].'" alt="">' : '';
                                                        ?></td>
                                                        <td class="wd-action" nowrap >
                                                            <a class="wd-edit" title="Edit" href="javascript:void(0)"
                                                               onclick="editContent('<?php echo $projectLivrableCategory['ProjectLivrableCategory']['id']; ?>',
                                                                   '<?php echo $value_encode; ?>', '<?php echo $projectLivrableCategory['ProjectLivrableCategory']['livrable_icon']; ?>', '<?php echo $projectLivrableCategory['ProjectLivrableCategory']['company_id']; ?>');">
                                                                <?php __('Edit') ?></a>
                                                            <div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $projectLivrableCategory['ProjectLivrableCategory']['id']), array('class' => 'wd-hover-advance-tooltip'), sprintf(__('Delete?', true), $projectLivrableCategory['ProjectLivrableCategory']['livrable_cat'])); ?>
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

                                    <?php /* 				<div class="wd-paging">

                                      <?php echo $paginator->prev(__("Prev", true), array('class'=>'wd-prev', 'tag' => 'span'), __("Prev", true), array('class'=>'wd-prev wd-disable', 'tag' => 'span'));?>
                                      <?php echo $paginator->numbers(array("separator" => " ")); ?>
                                      <?php echo $paginator->next(__("Next", true), array('class' => 'wd-next', 'tag' => 'span'), __("Next", true), array('class'=>'wd-next wd-disable', 'tag' => 'span'));?>
                                     */ ?>

                                    <div class="wd-add-employee">
                                    </div>
                                    <?php echo $this->Form->create('ProjectLivrableCategory', array("action" => "edit", 'id' => 'ProjectLivrableCategoryEditForm')); ?>
                                    <?php echo $validation->bind("ProjectLivrableCategory", array('form' => '#ProjectLivrableCategoryEditForm')); ?>
                                    <?php echo $this->Session->flash(); ?>
                                    <fieldset>
                                        <div class="wd-scroll-form-min" style="height: 100px;">
                                            <div class="wd-left-content">
                                                <div class="wd-input ">
                                                    <label for="last-name"><?php __("Name") ?></label>
                                                    <?php
                                                    echo $this->Form->input('livrable_cat', array('name' => 'data[ProjectLivrableCategory][livrable_cat]',
                                                        'type' => 'text',
                                                        'div' => false,
                                                        'label' => false,
                                                        "class" => "placeholder", "placeholder" => __("Name (*)", true)));
                                                    ?>
                                                </div>
                                                <div class="wd-input ">
                                                    <label for="last-icon"><?php __("Icon") ?></label>
                                                    <select name="data[ProjectLivrableCategory][livrable_icon]" id="ProjectLivrableCategoryIcon" style="width: 10%">
                                                        <option value="z0g.svg" data-thumb="z0g.svg"></option>
                                                        <option value="Access.svg" data-thumb="Access.svg"></option>
                                                        <option value="Android.svg" data-thumb="Android.svg"></option>
                                                        <option value="Apple.svg" data-thumb="Apple.svg"></option>
                                                        <option value="Behance.svg" data-thumb="Behance.svg"></option>
                                                        <option value="dropbox.svg" data-thumb="dropbox.svg"></option>
                                                        <option value="Excel.svg" data-thumb="Excel.svg"></option>
                                                        <option value="facebook.svg" data-thumb="facebook.svg"></option>
                                                        <option value="Instagram.svg" data-thumb="Instagram.svg"></option>
                                                        <option value="Linkedin.svg" data-thumb="Linkedin.svg"></option>
                                                        <option value="Messenger.svg" data-thumb="Messenger.svg"></option>
                                                        <option value="Microsoft.svg" data-thumb="Microsoft.svg"></option>
                                                        <option value="microsoft-teams.svg" data-thumb="microsoft-teams.svg"></option>
                                                        <option value="onedrive.svg" data-thumb="onedrive.svg"></option>
                                                        <option value="onenote.svg" data-thumb="onenote.svg"></option>
                                                        <option value="Pinterest.svg" data-thumb="Pinterest.svg"></option>
                                                        <option value="Powerpoint.svg" data-thumb="Powerpoint.svg"></option>
                                                        <option value="Publisher.svg" data-thumb="Publisher.svg"></option>
                                                        <option value="skype.svg" data-thumb="skype.svg"></option>
                                                        <option value="slack.svg" data-thumb="slack.svg"></option>
                                                        <option value="trello.svg" data-thumb="trello.svg"></option>
                                                        <option value="twitter.svg" data-thumb="twitter.svg"></option>
                                                        <option value="Word.svg" data-thumb="Word.svg"></option>
                                                        <option value="Youtube.svg" data-thumb="Youtube.svg"></option>
                                                        <!--<option value="i-zip.png" data-thumb="i-zip.png"></option>-->
                                                    </select>
                                                </div>
                                            </div>
                                            <?php echo $this->Form->input('id', array('name' => 'data[ProjectLivrableCategory][id]', 'type' => 'hidden')); ?>
                                            <?php echo $this->Form->input('company_id', array('name' => 'data[ProjectLivrableCategory][company_id]', 'value' => $company_id, 'type' => 'hidden')); ?>
                                            <?php echo $this->Form->input('view', array('name' => 'data[ProjectLivrableCategory][view]', 'value' => $view, 'type' => 'hidden')); ?>
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
                                            <a href="javascript:void(0)" id="<?php echo $reset ?>" class="wd-reset"><?php __('Reset') ?></a>
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
    <div align="center"><br/>
        <strong>You do not have permission to access.<br/><br/>
    </div>
<?php } ?>
<script>
<?php if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"])))) { ?>
        $("#reset_form_1").click(function(){
            $("input[id='ProjectStatusName']").val('');
            $("#ProjectLivrableCategoryId").val("");
            $("#title_form_update_city").html("<?php __("Add a new deliverable category") ?>");
            $("#ProjectLivrableCategoryLivrableCat").val("");
            $("#flashMessage").hide();
            $(".error-message").hide();
            $("div.wd-input,input,select").removeClass("form-error");
        });
<?php } ?>
    $('#btnSave').click(function(){
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    })
    function editContent(project_city_id, project_city_name, project_icon, company_id){
        $("#ProjectLivrableCategoryId").val(project_city_id);
        $("#ProjectLivrableCategoryLivrableCat").val(project_city_name);
        if(project_icon && project_icon !== undefined){
            $(".select2-selection__rendered").find('img').attr("src","/img/new-icon/project_document/" + project_icon);
        } else {
            $(".select2-selection__rendered").find('img').attr("src","/img/new-icon/project_document/z0g.svg");
        }
        $("#ProjectLivrableCategoryCompanyId").val(company_id);
        $("#title_form_update_city").html("<?php __("Edit the deliverable category") ?>");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    }
    $("#reset_form").click(function(){
        $("input[id='ProjectStatusName']").val('');
        $("#ProjectLivrableCategoryId").val("");
        $("#ProjectLivrableCategoryCompanyId").val('');
        $("#title_form_update_city").html("<?php __("Add a new deliverable category") ?>");
        $("#ProjectLivrableCategoryLivrableCat").val("");
        $("#flashMessage").hide();
        $(".error-message").hide();
        $("div.wd-input,input,select").removeClass("form-error");
    });
    function productStyles(selection) {
        if (!selection.id) { return selection.text; }
        var thumb = $(selection.element).data('thumb');
        if(!thumb){
            return selection.text;
        } else {
            var $selection = $('<img src="/img/new-icon/project_document/' + thumb + '" alt="">');
            return $selection;
        }
    }

    $("#ProjectLivrableCategoryIcon").select2({
        templateResult: productStyles,
        templateSelection: template
    });
    function template(data, container) {
        // console.log(data);
        // console.log(container);
        // $()
        var thumb = data.id;
        var $selection = $('<img src="/img/new-icon/project_document/' + thumb + '" alt="">');
        return $selection;
        return data.id;
    }
</script>
