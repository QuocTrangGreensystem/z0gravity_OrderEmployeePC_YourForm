<?php echo $html->css('uniform.default'); ?>
<?php echo $html->css('common-value'); ?>
<?php echo $html->script('jquery.mCustomScrollbar'); ?>
<?php echo $html->script('jquery.uniform.min'); ?>
<?php echo $html->script('common-value'); ?>

                <div class="wd-container-created-value">
                    <h1><?php echo $project_name ?> </h1>
                    <div class="wd-container-block-list">

                        <form id="createdValue" action="#">
                            <input type="hidden" value="<?php echo $id ?>" name="data[id]" />
                            <input type="hidden" value="<?php echo $project_id ?>" name="data[project_id]" />
                            <div class="wd-block-list wd-block-list-01">
                                <span class="wd-title-circle"></span>
                                <?php
                                $langCode = Configure::read('Config.langCode');
                                ?>
                                <h2 class="wd-title-value"><?php __('financial') ?></h2>
                                <p class="wd-question-value"><?php __('How should the project appear to our stakeholders?') ?></p>
                                <div id="wd-container-scroll-01" class="wd-container-scroll">
                                    <div class="customScrollBox">
                                        <div class="container">
                                            <div class="content">
                                                <fieldset class="wd-check-box-value"> 
                                                    <?php
                                                    foreach ($created_values as $created_value):
                                                        if ($created_value['ProjectCreatedValue']['type_value'] == 'financial'):
                                                            ?>
                                                            <div class="wd-check-box">
                                                                <label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> (<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</label>
                                                            </div>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="dragger_container">
                                            <div class="dragger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wd-block-list wd-block-list-02">
                                <span class="wd-title-circle"></span>
                                <h2 class="wd-title-value"><?php __('CUSTOMER') ?></h2>
                                <p class="wd-question-value"><?php __('How should the project appear to our customers?') ?></p>
                                <div id="wd-container-scroll-02" class="wd-container-scroll">
                                    <div class="customScrollBox">
                                        <div class="container">
                                            <div class="content">
                                                <fieldset class="wd-check-box-value">
                                                    <?php
                                                    foreach ($created_values as $created_value):
                                                        if ($created_value['ProjectCreatedValue']['type_value'] == 'customer'):
                                                            ?>
                                                            <div class="wd-check-box">
                                                                <label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> (<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</label>
                                                            </div>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="dragger_container">
                                            <div class="dragger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wd-block-list wd-block-list-03">
                                <span class="wd-title-circle"></span>
                                <h2 class="wd-title-value"><?php __('LEARNING &amp; GROWTH') ?></h2>
                                <p class="wd-question-value"><?php __('How can we sustain our ability to change and Improve?') ?></p>
                                <div id="wd-container-scroll-03" class="wd-container-scroll">
                                    <div class="customScrollBox">
                                        <div class="container">
                                            <div class="content">
                                                <fieldset class="wd-check-box-value">
                                                    <?php
                                                    foreach ($created_values as $created_value):
                                                        if ($created_value['ProjectCreatedValue']['type_value'] == 'learning'):
                                                            ?>
                                                            <div class="wd-check-box">
                                                                <label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> (<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</label>
                                                            </div>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="dragger_container">
                                            <div class="dragger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wd-block-list wd-block-list-04">
                                <span class="wd-title-circle"></span>
                                <h2 class="wd-title-value"><?php __('BUSINESS PROCESS') ?></h2>
                                <p class="wd-question-value"><?php __('What business processes must the project excel at?') ?></p>
                                <div id="wd-container-scroll-04" class="wd-container-scroll">
                                    <div class="customScrollBox">
                                        <div class="container">
                                            <div class="content">
                                                <fieldset class="wd-check-box-value">
                                                    <?php
                                                    foreach ($created_values as $created_value):
                                                        if ($created_value['ProjectCreatedValue']['type_value'] == 'business'):
                                                            ?>
                                                            <div class="wd-check-box">
                                                                <label><input type="checkbox" rel="<?php echo $created_value['ProjectCreatedValue']['value'] ?>" name="data[created_value][]" class="checkbox" value="<?php echo $created_value['ProjectCreatedValue']['id'] ?>" /><?php echo $created_value['ProjectCreatedValue']['description'] ?> (<?php echo $created_value['ProjectCreatedValue']['value'] ?>)</label>
                                                            </div>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="dragger_container">
                                            <div class="dragger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wd-block-created-value">
                                <p><span id="value_sum">0</span><span id="value_sum_total">/<?php echo $totalValue['ProjectCreatedValue']['total'];?></span></p>
                            </div>
                            <input type="hidden" id="sum_hidden" value="0" name="data[value]" />
                        </form>
                    </div>
                </div>
<script>
    $(function(){
        var $form = $('#createdValue'), canModified = '';
        if(!canModified){
            $(".wd-check-box-value .wd-check-box input").attr('disabled' , true);
        }
        $form.find(':checkbox').click(function(){
			return false;
			});
        tong = 0;
        $("#createdValue input[name='data[created_value][]']").each(function(){
<?php if (!empty($projectC['ProjectCreatedVal'])): ?>

    <?php foreach ($projectC['ProjectCreatedVal'] as $key => $value): ?>
                        if($(this).val() == '<?php echo $value; ?>') {
                            $(this).attr('checked','checked');
                            $(this).parent().addClass('checked');
                            tong += Number($(this).attr('rel'));
                        }
                            
    <?php endforeach; ?>
<?php endif; ?>  
        });
        $("#value_sum").html(tong);
        $("#sum_hidden").val(tong);
    });
</script>