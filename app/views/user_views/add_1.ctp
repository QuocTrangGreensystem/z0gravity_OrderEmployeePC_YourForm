<style>
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
                    <form method="post" id="xml_form" action="<?php echo $html->url('/user_views/add') ?>" >
                        <div class="wd-new-list">
                            <div class="wd-multi-select-new-left" style="height: 340px; overflow: auto">
                                <h3 class="wd-tt" rel="project_detail"><?php echo __("Projects Details", true); ?></h3>
                                <div class="wd-list-new" rel="project_detail" >
                                    <?php foreach ($project_detail_fields as $key => $field) { ?>
                                        <?php
                                        if ($key != 'id') {
                                            if ($key == 'project_name') {
                                                $selected = 'wd-selected';
                                                $requi = '(*)';
                                            } else {
                                                $selected = '';
                                                $requi = '';
                                            }
                                            ?>
                                            <?php if ($key != 'copy_number'): ?> 
                                                <p id="<?php echo $key ?>_left" class="<?php echo $selected ?>" rel="<?php echo $key ?>"><?php echo __("$field $requi", true); ?></p>
                                                <?php
                                            endif;
                                        }
                                    }
                                    ?>
                                </div>
                                <h3 class="wd-tt" rel="project_detail"><?php echo __("AMR", true); ?></h3>
                                <div class="wd-list-new" rel="project_detail" >
                                    <?php
                                    foreach ($project_amr_fields as $key => $field):
                                        if ($key != 'id_amr'):
                                            $rel_key = $key;
                                            $rel_key = substr($rel_key, 0, -4);
                                            ?>
                                            <p id="<?php echo $key; ?>_left" rel="<?php echo $rel_key; ?>"><?php echo __("$field", true); ?></p>
                                            <?php
                                        endif;
                                    endforeach;
                                    ?>
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
                                        <p id="project_name_right" rel="project_name" style="color:green"><?php echo __("Project Name (*)", true); ?></p>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<?php echo $html->script('common'); ?>
<!--[if IE]>
<?php echo $html->script('excanvas.compiled'); ?>
<![endif]-->
<script>
    $(document).ready(function(){
        $('div.empty-amr').remove();
        $(".wd-selected-new-right .wd-selected-list p").append($(".overlay").clone());
        $(".wd-selected-new-right .wd-selected-list p .overlay").show();
    });
    
    $("div.wd-list-new p, .wd-selected-new-right .wd-selected-list p").on("click",function(){
        $('div.empty-amr').remove();
        if ($(this).hasClass("wd-selected")){
            return false;
        }
        if ($(this).hasClass("wd-activated")){
            $(this).removeClass("wd-activated");            
        }else{
            $(this).addClass("wd-activated");            
        }
    });
    
    $(".wd-selected-new-right .wd-selected-list p .overlay .wd-up").on("click", function(){
        $('div.empty-amr').remove();
        move_up($(this).parent().parent()); 
    });
    
    $(".wd-selected-new-right .wd-selected-list p .overlay .wd-down").on("click", function(){
        $('div.empty-amr').remove();
        move_down($(this).parent().parent()); 
    });
    
    function move_up(obj) {
        var upper = $(obj).prev();
        if (upper.length > 0) {
            $(obj).parent().find("p").removeClass('wd-activated');
            $(upper).before($(obj).clone());
            // highlight
            $(upper).prev().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                $(this).animate({backgroundColor:'#FFF'}, 'slow');
            });
            $(obj).remove();   
        }else{
            // switch category
            var curCat = $(obj).parent();
            var index = $(".wd-selected-new").index(curCat);
            var prevCat = $(".wd-selected-new:eq(" + (index - 1) + ")");
            if (prevCat.length > 0) {
                var ob = $(prevCat).append($(obj).clone());
                $(obj).remove();
                $(prevCat).find("p:last").animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                    $(this).animate({backgroundColor:'#FFF'}, 'slow');
                });
            }
        }
    }
    
    function move_down(obj) {
        var prever = $(obj).next();
        if (prever.length > 0) {
            $(obj).parent().find("p").removeClass('wd-activated');
            $(prever).after($(obj).clone());
            // highlight
            $(prever).next().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                $(this).animate({backgroundColor:'#FFF'}, 'slow'); 
            });
            $(obj).remove();   
        }else{
            // switch category
            var curCat = $(obj).parent();
            var index = $(".wd-selected-new").index(curCat);
            var prevCat = $(".wd-selected-new:eq(" + (index + 1) + ")");
            if (prevCat.length > 0) {
                $(prevCat).prepend($(obj).clone());
                $(obj).remove();
                $(prevCat).find("p:eq(0)").animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                    $(this).animate({backgroundColor:'#FFF'}, 'slow');
                });
            }
        }
    }
    
    function add_item2(target) {
        $(target).removeClass("wd-activated");
        var rel = $(target).parent().attr("rel");
    }
	
    function add_item(target) {
        $(target).removeClass('wd-activated');
        var id_target = $(target).attr("id");
        var tmp = id_target.substr(0, id_target.lastIndexOf("_", id_target.length));
        var add_item_content = '<p id="' + tmp + '_right" rel="' + tmp + '" style="position:relative;">' + $(target).text() + '</p>';
        var overlay_div = '<div class="overlay" style="display: block; "><span class="wd-up"></span><span class="wd-down"></span></div>';
        var title_right_avail = ($(".wd-selected-new-right h3[rel='" + $(target).parent().attr("rel") + "']").length != 0);
        var des_list_p =  $(".wd-selected-new-right .wd-selected-list div[rel='" + $(target).parent().attr("rel") + "']");
		
        if (title_right_avail) {
            // add zo
            if ($("#" + tmp + "_right").length == 0) {
                $(des_list_p).append(add_item_content);
                $("#" + tmp + "_right").append(overlay_div);
                $(target).addClass("wd-selected");
            }
        }else{
            // them div duoi title
            //	$(".wd-selected-new-right .wd-selected-list").append($(".wd-multi-select-new-left h3[rel='" + $(target).parent().attr("rel") + "']").clone());
            $(".wd-selected-new-right .wd-selected-list").append('<div class="wd-selected-new" rel="' + $(target).parent().attr("rel") + '"></div>');
            des_list_p = $(".wd-selected-new-right .wd-selected-list div[rel='" + $(target).parent().attr("rel") + "']");
            if ($("#" + tmp + "_right").length == 0) {
                $(des_list_p).append(add_item_content);
                $("#" + tmp + "_right").append(overlay_div);
                $(target).addClass("wd-selected");
            }
        }
        //return false;
    }
    
    function remove_item(target) {
        $(target).removeClass('wd-activated');
        $(target).find('.overlay').remove();
        var obj = $(target);
        var rel = $(target).parent().attr("rel");
        var parent_obj = $(target).parent();
        // remove obj
        $(obj).remove();
        // restore left-list item
        var tmp = $(target).attr("id").substr(0, $(target).attr("id").lastIndexOf("_", $(target).attr("id").length));
        $("#" + tmp + "_left").removeClass("wd-selected");
        // delete parent list when empty
        if ($(parent_obj).children().length < 1) {
            $(".wd-selected-new-right .wd-selected-list [rel='" + rel +  "']").remove();
        }
    }
    
    // add - double click
    $(".wd-multi-select-new-left .wd-list-new p").dblclick(function(){
        $('div.empty-amr').remove();
        add_item($(this));
    });
        
    // remove - double click
    $(".wd-selected-new-right .wd-selected-list p").on("dblclick",function(){
        $('div.empty-amr').remove();
        if ($(this).html().search("Project Name") == -1) {	
            $(this).find(".overlay").remove();
            remove_item($(this));
        }
    });
    
    $("a.wd-next").click(function(){
        $('div.empty-amr').remove();
        $(".wd-multi-select-new-left .wd-list-new p.wd-activated").each(function(){
            add_item($(this));
        });
    });
    
    $("a.wd-prev").click(function(){
        $('div.empty-amr').remove();
        $(".wd-selected-new-right .wd-selected-list p.wd-activated").each(function(){
            if ($(this).html().search("Project Name") == -1) {
                remove_item($(this));
            }
        });
    });
    
    $("#btnSave").click(function(){
        $('div.empty-amr').remove();
        $("#view-name").parent().css("border","1px solid #E0E0E0");
        if ($("#view-name").val() == "") {
            var errorElement = $("#view-name").parent();
            errorElement.css("border","1px solid red");
            $("#view-name").attr("placeholder","The name of view is not blank");
            $('.error').append('<div class="empty-amr" style="color:red;margin-left:-9px;padding-top:10px;">The name of view is not blank</div>');
            return false;
        }
        else return gen_result();
    });
	
    function gen_result() {
        var head_list = $(".wd-selected-new-right .wd-selected-list .wd-tt");
        var xml = "<user_view>";
        $(head_list).each(function(){
            var tag = $(this).attr("rel");
            var obj2 = $(".wd-selected-new-right .wd-selected-new[rel='" + tag + "']");
            $(obj2).find("p").each(function(){
                var t = $(this).clone();
                $(t).find(".overlay").remove();
                var alias = $(t).text();
                if (alias == 'Project Name (*)') alias = 'Project Name';
                var name = $(this).attr("rel");
                if (name == "project_manager_id1" || name == "project_manager_id2") name = "project_manager_id";
                if (name == "weather1" || name == "weather2") name = "weather";
                xml += '<' + tag + ' ' + name + ' = "' + alias + '" />';
            }); 
        });
        xml += "</user_view>";
        /*if (xml.search('project_amr') == -1) {
                        $('.error').append('<div class="empty-amr" style="color:red;margin-left:-9px;padding:10px;">Please choose at least 1 AMR field</div>');
                        return false;
                }*/
        $("#xml_data").val(xml);
        return true;
    }

</script>