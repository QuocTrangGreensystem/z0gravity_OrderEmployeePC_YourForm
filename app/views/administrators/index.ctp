
<?php //=====================================================================  

?>
<div id="wd-main-contant">
    <div class="wd-tab" style="width: 1107px;">
        <div class="wd-panel">
            <div class="wd-section" id="wd-fragment-1">
                <fieldset>
                    <h1 class="wd-title"><?php __('Project Livrable Categories') ?></h1>
                    <div class="wd-input">
                        <div class="wd-table">
                            <table cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th class="wd-left"> <?php __('Livrable Cat') ?>Livrable Cat</th>
                                    <th>Actions</th>
                                    </tr>
                                </thead>
                                <?php
                                $i = 0;
                                foreach ($projectLivrableCategories as $projectLivrableCategory):
                                    $class = null;
                                    if ($i++ % 2 == 0) {
                                        $class = ' class="altrow"';
                                    }
                                    ?>
                                    <tbody>

                                        <tr>
                                        <td><?php echo $projectLivrableCategory['ProjectLivrableCategory']['id']; ?>&nbsp;</td>
                                        <td><?php echo $projectLivrableCategory['ProjectLivrableCategory']['livrable_cat']; ?>&nbsp;</td>                               
                                        <td> 
                                            <a class="btn_edit" title="Edit" href="javascript:void(0)" 
                                               onclick="editProjectLivrableCategory('<?php echo $projectLivrableCategory['ProjectLivrableCategory']['id']; ?>',
                                                   '<?php echo $projectLivrableCategory['ProjectLivrableCategory']['livrable_cat']; ?>');">
                                                <?php __('Edit') ?></a>
                                            <?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $projectLivrableCategory['ProjectLivrableCategory']['id']), array('class' => 'btn_delete', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete # %s?', true), $projectLivrableCategory['ProjectLivrableCategory']['id'])); ?>
                                        </td>
                                        </tr>
                                    </tbody>
                                <?php endforeach; ?>
                            </table> 
                        </div>
                    </div>
                    <div class="wd-project-employ">
                        <?php echo $this->Session->flash(); ?>
                        <?php echo $this->Form->create('ProjectLivrableCategory', array("action" => "edit", 'id' => 'ProjectLivrableCategoryEditForm')); ?>
                        <?php echo $validation->bind("ProjectLivrableCategory", array('form' => '#ProjectLivrableCategoryEditForm')); ?>
                        <h1 class="wd-title" id="title_form_update_project_livrable_category"><?php __("Add new project livrable") ?></h1>
                        <div class="wd-add-project"> 
                            <div class="wd-left-content" style="width:940px;">
                                <div class="wd-input wd-input-80 wd-lable-80" style="margin: 5px 8px 0px 6px;">
                                    <?php
                                    echo $this->Form->input('livrable_cat', array('name' => 'data[ProjectLivrableCategory][livrable_cat]',
                                        'type' => 'text',
                                        'div' => false,
                                        'label' => false,
                                        'style' => ' width:150px ',
                                        "class" => "placeholder", "placeholder" => "Livrable Cat (*)"));
                                    ?>
                                </div>
                                <?php echo $this->Form->input('id', array('name' => 'data[ProjectLivrableCategory][id]', 'type' => 'hidden')); ?>
                            </div>                   
                        </div>                        
                    </div>
                </fieldset>
                <fieldset>
                    <div class="wd-submit" style="float: none; text-align:center; padding:15px 0px; width:950px">
                        <input type="hidden" name="data[Project][tab_index]" id="project_tab_index"/>
                        <input type="submit" value="<?php __("Save") ?>" />
                        <input type="reset" id="reset_button" value="<?php __("Reset") ?>" />
                    </div>
                </fieldset>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<script language="javascript"> 
    function editProjectLivrableCategory(project_livrable_category_id, project_livrable_category_name){
        $("#ProjectLivrableCategoryId").val(project_livrable_category_id);
        $("#ProjectLivrableCategoryLivrableCat").val(project_livrable_category_name);
        $("#title_form_update_project_livrable_category").html("<?php __("Edit Livrable Category") ?>");
        $("#flashMessage").hide();
    }
    $("#reset_button").click(function(){
        $("#ProjectLivrableCategoryId").val("");
        $("#title_form_update_project_livrable_category").html("<?php __("Add new project livrable") ?>");
        $("#ProjectLivrableCategoryLivrableCat").val("<?php __("Livrable Category (*)") ?>");
    });
</script>