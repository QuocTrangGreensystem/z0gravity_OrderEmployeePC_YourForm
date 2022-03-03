<?php echo $html->css('uniform.default'); ?>
<?php echo $html->css('common-value'); ?>
<?php echo $html->script('jQuery.easing1.3'); ?>
<?php echo $html->script('jquery.mousewheel'); ?>
<?php echo $html->script('jquery.mCustomScrollbar'); ?>
<?php echo $html->script('jquery.uniform.min'); ?>
<?php echo $html->script('common-value'); ?>
<style>
     body .wd-tab .wd-panel{
        border: none;
    }
<?php if( $isMobile || $isTablet ): ?>
    .wd-container-created-value .wd-container-block-list {
        height: auto;
        margin: 64px 0 0 0;
        padding: 2% 0 0 0;
    }
    .wd-container-created-value .wd-container-block-list .wd-block-list {
        position: static !important;
        background: #ededed;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
        height: auto;
        margin-bottom: 2%;
        padding: 2%;
        width: 40%;
        <?php if( $isTablet ): ?>
        float: left;
        margin-right: 2%;
        <?php endif ?>
    }
    .wd-container-created-value .wd-container-block-list .wd-block-list .wd-title-circle {
        position: static !important;
        float: right;
    }
    .wd-container-scroll {
        margin: 10px 0 0 0;
        <?php if( $isTablet ): ?>
        height: 130px;
        overflow: auto;
        <?php else: ?>
        height: auto;
        <?php endif ?>
    }
    .wd-container-scroll .dragger_container {
        display: none;
    }
    .wd-container-scroll .customScrollBox .container {
        width: auto;
        float: none;
    }
    .wd-container-created-value .wd-container-block-list .wd-block-created-value {
        top: -54px;
        left: 0;
        width: 130px;
        height: auto;
        background-position: center center;
        padding: 5px 0;
    }
    .wd-container-created-value {
        width: auto;
    }
    .wd-main-content {
        width: auto;
        min-width: auto;
    }
    .wd-section {
        padding: 0 20px;
    }
    /*@media screen and () {

    }*/
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
   
<?php endif ?>
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">

        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-section">
                <?php echo $this->Session->flash(); ?>
                <div class="wd-container-created-value">
                    <h1 style="color: orange"><?php echo $project_name ?> </h1>
                    <div class="wd-container-block-list" style="">
                        <form id="createdValue" action="#">
                            <div class="wd-block-created-value">
                                <p><span id="value_sum">0</span><span id="value_sum_total">/<?php echo $totalValue['ProjectCreatedValue']['total'];?></span></p>
                            </div>
                            <input type="hidden" value="<?php echo $id ?>" name="data[id]" />
                            <input type="hidden" value="<?php echo $project_id ?>" name="data[project_id]" />
                            <div class="wd-block-list wd-block-list-01">
                                <span class="wd-title-circle"></span>
                                <h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Financial', true); ?></h2>
                                <p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our stakeholders?', true) ?></p>
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
                                <h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Customer', true); ?></h2>
                                <p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'How should the project appear to our customers?', true) ?></p>
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
                                <h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Learning & Growth', true); ?></h2>
                                <p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'How can we sustain our ability to change and Improve?', true) ?></p>
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
                                <h2 class="wd-title-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'Business Process', true); ?></h2>
                                <p class="wd-question-value"><?php echo __d(sprintf($_domain, 'Created_Value'), 'What business processes must the project excel at?', true) ?></p>
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
                            <input type="hidden" id="sum_hidden" value="0" name="data[value]" />
                        </form>
                    </div>
                </div>
            </div>
            </div></div>

        </div>
    </div>
</div>
<script>
// var wdTable = $('.wd-container-block-list');
// var heightTable = $(window).height() - wdTable.offset().top - 40;
// //heightTable = (heightTable < 500) ? 500 : heightTable;
// wdTable.css({
//     height: heightTable,
// });
// $(window).resize(function(){
//     heightTable = $(window).height() - wdTable.offset().top - 40;
//     //heightTable = (heightTable < 500) ? 500 : heightTable;
//     wdTable.css({
//         height: heightTable,
//     });
// });
    $(function(){
        var $form = $('#createdValue'), canModified = '<?php echo ($canModified && !$_isProfile ) || ($_isProfile && $_canWrite); ?>';
        if(!canModified){
            $(".wd-check-box-value .wd-check-box input").attr('disabled' , true);
        }
        $form.find(':checkbox').click(function(){

            var sum = 0;
            var result = [];
            $form.find("input[type=checkbox]:checked").each(function(){
                sum +=parseInt($(this).attr('rel'));
                result.push($(this).attr('rel'));
            });
            b = null;
            if ($(this).is(':checked')) {
                $("#sum_hidden").val(sum);
                counter = Number($("#value_sum").text());
                var a = function(){
                    $("#value_sum").html(counter++);
                    if(counter <= sum){
                        clearTimeout(a1);
                        b =  setTimeout(a, 150);
                    }
                };
                a();
            }else{

                $("#sum_hidden").val(sum);
                counter = Number($("#value_sum").text());
                $("#value_sum").html("");
                var a1 = function(){
                    $("#value_sum").html(counter--);
                    if(counter >= sum){
                        clearTimeout(b);
                        setTimeout(a1, 100);
                    }
                };
                a1();
            }

            $form = $("#createdValue");
            $.ajax({
                type:'POST',
                url: "<?php echo $html->url(array("controller" => "project_created_vals", "action" => "saveCreated")) ?>" ,
                data:$form.serializeArray(),
                cache: false,
                success:function(content){
                    // alert("asdd");
                }
            });
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
