<style>
    .wd-selected-new .wd-selected{
        color: green;
    }
    .wd-multi-select-new-left .wd-list-new p, .wd-selected-list p {
        color: #000000;
        cursor: default;
        /*background: #CCCCCC;*/
        padding: 5px;
        border: 1px solid #C3C3C3;
        margin-top: 2px;
        position: relative;
    }
    .wd-multi-select-new-left .wd-list-new p.wd-selected {
        color: #C3C3C3;
        cursor: default;
        /*background: #CCCCCC;*/
        padding: 5px;
        border: 1px solid #C3C3C3 ;
        margin-top: 2px;
    }

    p.wd-activated {
        color: #004686 !important;
        text-decoration: underline !important;
        cursor: default !important;
        /*background: #CCCCCC;*/
        padding: 5px !important;
        border: 1px solid #004686 !important;
        margin-top: 2px !important;
    }

    .wd-selected-list p, .wd-list-new p, .wd-tt, p {
        -moz-user-select: none;
        -khtml-user-select: none;
        user-select: none;
    }

    .overlay {
        position: absolute;
        width: 50px;
        height: 20px;
        display: none;
        top: 5px; right: 0;
        text-decoration: none;
    }
    #div_selected div.wd-selected-new p span{
        display: inline;
    }
</style>
<body>
    <div id="wd-container-main" class="wd-project-detail">
        <?php echo $this->element("project_top_menu") ?>
        <div class="wd-layout">

            <div class="wd-main-content">
                <div class="wd-list-project">
                    <div class="wd-title">
                        <h2 class="wd-t1"><?php echo __("New View", true); ?></h2>
                    </div>
                    <?php
                    $index = 0;
                    echo $this->Form->create('UserView', array('url' => '/user_views/add', 'inputDefaults' => array('label' => false, 'div' => false)));
                    ?>
                    <div class="wd-new-list">
                        <div class="wd-multi-select-new-left" style="height: 340px; overflow: auto">
                            <h3 class="wd-tt" rel="project_detail"><?php echo __("Projects Details", true); ?></h3>
                            <div class="wd-list-new" rel="project_detail" >
                                <?php foreach ($projectFields as $field => $name) : ?>
                                    <?php
                                    if ($field == 'Project.id') {
                                        continue;
                                    }
                                    $class = '';
                                    if ($field == 'Project.project_name') {
                                        $class = 'wd-selected';
                                    }
                                    ?>
                                    <p class="<?php echo $class ?>" rel="<?php echo $field ?>">
                                        <?php
                                        echo __($name, true);
                                        echo $this->Form->input('UserView.content.' . ($index++), array('type' => 'checkbox',
                                            'style' => 'display:none', 'value' => $field, 'checked' => !empty($class), 'hiddenField' => false));
                                        ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                            <h3 class="wd-tt" rel="project_detail"><?php echo __("AMR", true); ?></h3>
                            <div class="wd-list-new" rel="project_detail" >
                                <?php foreach ($amrFields as $field => $name) : ?>
                                    <?php
                                    if ($field == 'ProjectAmr.id' || $field == 'ProjectAmr.project_id') {
                                        continue;
                                    }
                                    ?>
                                    <p rel="<?php echo $field ?>">
                                        <?php
                                        echo __($name, true);
                                        echo $this->Form->input('UserView.content.' . ($index++), array('type' => 'checkbox',
                                            'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                        ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="wd-submit-select">
                            <a href="javascript:void(0);" class="wd-next"><?php echo __("Next", true); ?></a>
                            <a href="javascript:void(0);" class="wd-prev"><?php echo __("Prev", true); ?></a>
                        </div>
                        <div class="wd-selected-new-right">
                            <div class="wd-view">
                                <input type="text" id="view-name" name="data[UserView][name]" style="width: 100%" placeholder="View name" class="placeholder" />
                            </div>
                            <div class="wd-description">
                                <input type="text" id="view-desc" name="data[UserView][description]" style="width: 100%" placeholder="View description" class="placeholder" />
                            </div>
                            <div class="wd-selected-list" id="div_selected" style="height: 258px; overflow: auto">
                                <div class="overlay"><span class="wd-up"></span><span class="wd-down"></span></div>
                                <h3 class="wd-tt" rel="project_detail"><?php echo __("Personalized Views", true); ?></h3>
                                <div class="wd-selected-new" rel="project_detail">								
                                    <p rel="Project.project_name" class="wd-selected"><?php echo __("Project Name (*)", true); ?></p>
                                </div>
                            </div>
                            <div class="default-view" style="clear: both;text-align:center">
                                <input id="is-selected" style="float:left;" type="checkbox" name="data[UserView][is_selected]" />
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as default view", true); ?></p>
                            </div> 
                        </div>


                        <div class="error" style="clear: both;text-align:center"></div>
                        <fieldset>

                            <input type="hidden" id="xml_data" name="data[UserView][content]" />
                            <div class="wd-submit">
                                <input type="submit" class="wd-save" value="Save" id="btnSave" style="margin-left:18px"/>
                                <a class="wd-reset" id="reset_button" href="<?php echo $html->url('/user_views/add'); ?>"><?php echo __("Refresh", true); ?></a>
                            </div>

                        </fieldset>
                    </div>	
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</body>
<?php echo $html->script('common'); ?>
<!--[if IE]>
<?php echo $html->script('excanvas.compiled'); ?>
<![endif]-->
<script type="text/javascript">
    
    (function(){
        $(function(){
            $("div.wd-list-new p, .wd-selected-new-right .wd-selected-list p").on("click",function(){
                var $element = $(this);
                if ($element.hasClass("wd-selected")){
                    return false;
                }
                $element.toggleClass('wd-activated');
            });
            var $overlay = $('.wd-selected-list .overlay');
            $('.wd-submit-select .wd-next').click(function(){
                var $panel = $('.wd-selected-list .wd-selected-new');
                $('.wd-list-new .wd-activated').each(function(){
                    var $element = $(this);
                    var $clone = $element.clone();
                    $element.removeClass('wd-activated').addClass('wd-selected').find('input').prop('checked', true);
                    $panel.append($clone.html($clone.text()).append($overlay.clone().show()));
                });
            });
            $('.wd-submit-select .wd-prev').click(function(){
                $('.wd-selected-new .wd-activated').each(function(){
                    var $element = $(this);
                    $('.wd-list-new [rel="'+ $element.attr('rel')+ '"]').removeClass('wd-selected').find('input').prop('checked', false);
                    $element.remove();
                });
            });
        });
    })(jQuery);
    

</script>