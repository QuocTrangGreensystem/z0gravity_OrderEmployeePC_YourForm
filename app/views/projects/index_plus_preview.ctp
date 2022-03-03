<?php
echo $html->script(array(
    'jquery.validation.min',
    'html2canvas',
    'jquery.html2canvas.yourform',
    'vis.min',
    'dashboard/jqx-all',
    'dashboard/jqxchart',
));
echo $html->css(array(
    'gantt_v2_1',
    'business',
    'vis.min',
    // '3-col-portfolio',    
    'bootstrap.min',
    'preview/grid-project',
));
echo $this->element('dialog_detail_value');
echo $this->element('dialog_projects');
?>
<div class="wd-project-filter">
    <?php
        echo $this->Form->create('Category', array('style' => 'display: inline-block'));
        $href = '';
        $href = $this->params['url'];
        if(!empty($appstatus)){
            $op = ($appstatus == 1) ? 'selected="selected"' : '';
            $ar = ($appstatus == 3) ? 'selected="selected"' : '';
            $md = ($appstatus == 4) ? 'selected="selected"' : '';
            $io = ($appstatus == 5) ? 'selected="selected"' : '';
            $io2 = ($appstatus == 6) ? 'selected="selected"' : '';
        }
    ?>
        <select class="wd-customs" id="CategoryStatus" rel="no-history">
            <option value="0"><?php echo  __("--Select--", true);?></option>
        </select>
        <?php if($cate != 2):?>
        <select class="wd-customs" id="CategoryCategory" rel="no-history">
            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("In progress", true)?></option>
            <option value="6" <?php echo isset($io2) ? $io2 : '';?>><?php echo  __("Opportunity", true)?></option>
            <option value="5" <?php echo isset($io) ? $io : '';?>><?php echo  __("In progress + Opportunity", true)?></option>
            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Archived", true)?></option>
            <option value="4" <?php echo isset($md) ? $md : '';?>><?php echo  __("Model", true)?></option>
        </select>
        <?php endif;?>
    <?php
        echo $this->Form->end();
    ?>
    <div class="open-filter-form" onclick="openFilter();">
        <img title="header-bottom"  src="<?php echo $html->url('/img/new-icon/search.png'); ?>"/><span>Rechercher ...</span>
    </div>
    <div class="search-filter">
    <span class="close-filter"><img title="Close filter"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    <?php
        echo $this->Form->create('Filter', array('style' => 'display: inline-block'));
        $href = '';
        $href = $this->params['url']; ?>
            <?php if(!empty($listProgramFields)){?>
                <select class="wd-customs" id="project-program" rel="no-history">
                    <option value=""><?php echo  __("Type de projet", true)?></option>
                   <?php  foreach ($listProgramFields as $key => $value) {?>
                        <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                   <?php  } ?>
                </select>
            <?php } ?>
            <input type="text" name="project-name" placeholder="Project">
            <select class="wd-customs" id="weather" rel="no-history">
                <option value=""><?php echo  __("Météo", true)?></option>
                <option value="sun"><?php echo  __("Sun", true)?></option>
                <option value="rain"><?php echo  __("Rain", true)?></option>
                <option value="cloud"><?php echo  __("Cloud", true)?></option>
            </select>
            <?php 
            if(!empty($listProjectManager)){?>
                <select class="wd-customs" id="project-manager" rel="no-history">
                    <option value=""><?php echo  __("Chef de projet", true)?></option>
                   <?php  foreach ($listPMFields as $key => $value) {?>
                        <option value="<?php echo $value['id'] ?>"><?php echo $value['first_name'] .' '. $value['last_name']; ?></option>
                   <?php  } ?>
                </select>
            <?php } ?>
            
            <input type="text" name="avancement" placeholder="Avancement">
        <?php
        echo $this->Form->end();
    ?>
    </div>
</div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout wd-project-grid">
        <div class="container-fluid">
            <div class="row">
            <?php foreach ($listProjects as $listProject) { ?>
            <!-- Projects Row -->
            
                <?php foreach ($listProject as $project) { ?>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <div class='portfolio-item'>
                    <div class='portfolio-item-inner'>
                        <?php
                        $isDoc = false;
                        $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project['Project']['id'], '?' => array('sid' => $api_key)), true);
                        if (!empty($globals[$project['Project']['id']]['global']) && $globals[$project['Project']['id']]['file'] == false) {
                            if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $globals[$project['Project']['id']]['global']['ProjectGlobalView']['attachment'])) {
                                $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                                $isDoc = true;
                            }
                        } else {
                            $link = '';
                        }
                        if(!$isDoc){?>
                        <div class="open-popup" data-id="<?php echo $project['Project']['id'] ?>"  style="width: 100%; overflow: hidden;">
                            <img width="435" height="250" style="margin: auto" src="<?php echo (!empty($link) ? $link : '') ?>" alt="">
                        </div>
                        <?php }else{ ?>
                        <div class="">
                            <iframe src="<?php echo $link; ?>" class="img-responsive" style="width: 100%;height: 252px;"></iframe>
                            <div data-id="<?php echo $project['Project']['id'] ?>"  class="overlay"></div>
                        </div>
                        <?php } ?>
                        <div class="project-item-content">
                            <?php 
                                $full_name = trim($listProjectManager[$project['Project']['id']]['first_name']) .' '.trim($listProjectManager[$project['Project']['id']]['last_name']);
                                $name = substr( trim($listProjectManager[$project['Project']['id']]['first_name']),  0, 1) .''.substr( trim($listProjectManager[$project['Project']['id']]['last_name']),  0, 1);

                            ?>
                            <span class="project-manager-name circle-name"><?php echo $name; ?></span>
                            <div class="text-ellipsis">
                                <a href="<?php echo $html->url('/' . $ACLController . '/' . $ACLAction .'/'.$project['Project']['id']) ?>" target="_blank"><?php echo $project['Project']['project_name'] ?></a>
                            </div>
                            <p class="project-description"><?php echo $logs[ $project['Project']['id'] ]['ProjectAmr']['description']; ?></p>
                            <?php $weather = !empty($listWeather[$project['Project']['id']]) ? ($listWeather[$project['Project']['id']] . '.png') : 'sun.png';
                                $rank = !empty($listRank[$project['Project']['id']]) ? ($listRank[$project['Project']['id']] . '.png') : 'up.png';
                                $class = !empty($listRank[$project['Project']['id']]) ? $listRank[$project['Project']['id']] : '';
                                $projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($project['Project']['end_date']));
                                $projectDate = abs(strtotime($project['Project']['end_date']) - strtotime($project['Project']['start_date']));
                                $initDate = 100;
                                if($projectCurrentDate < $projectDate) $initDate = floor(($projectCurrentDate / $projectDate) * 100);
                            ?>
                            <div class="project-item-weather <?php echo $class; ?> ">
                                <img src="<?php echo $html->url('/img/new-icon/' . $weather) ?>">
                                <div class="project-item-list-action">
                                    <ul class="list-inline">
                                        <li><span><?php echo $listProgram[$project['Project']['id']];?></span></li>
                                        <li><a href="/projects/index_preview/<?php echo $project['Project']['id'];?>?view=new"><img title="Dashboard" src="<?php echo $html->url('/img/new-icon/dashboard.png'); ?>"/></a></li>
                                        <li><a href="/project_phases/"><img title="<?php __('Open project');?>" src="<?php echo $html->url('/img/new-icon/screen-admin.png'); ?>"/></a></li>
                                    </ul>
                                    <div class="project-progress">
                                        <p class="progress-full"><span class="progress-content" style="width: <?php echo $initDate; ?>%"></span></p>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </div>
                        
                    </div>
                    </div>
                    </div>
                <?php } ?>
            <?php } ?>
            </div>
            <!-- /.row -->
            <a style="" class="loadmore" href="#"><img title="Load more" src="<?php echo $html->url('/img/new-icon/down.png'); ?>"/></a>
        </div>
    </div>
</div>
<div id="loading_w_plus"><p></p></div>
<div id='mock'></div>
<script type="text/javascript">
var savePosition = <?php echo $savePosition ?>;
$('.open-popup').on('click', function(){
    var id = $(this).attr('data-id');
    showProjectDetail(id);
});
$('.overlay').on('click', function(){
    var id = $(this).attr('data-id');
    showProjectDetail(id);
});

$(window).resize(function(){
    var width  = $(window).width(); 
    if(width <= 1366){
        $('#wd-container-main').css({'width': width});
    }
});
$("#dialogDetailValue").draggable({
    cancel: "#wd-tab-content",
    stop: function(event, ui){
        var position = $("#dialogDetailValue").position();
        $.ajax({
            url : "/projects/savePopupPosition",
            type: "POST",
            data: {
                top: position.top,
                left: position.left
            }
        });
        savePosition = position;
    }
});
function showProjectDetail(id){
    jQuery.ajax({
        url : "/projects/ajax/"+id,
        type: "GET",
        cache: false,
        beforeSend: function(){
            $('#loading_w_plus').show();
        },
        success: function (html) {
            $('#loading_w_plus').hide();
            showMe();
            jQuery('#contentDialog').html(html);
            var width  = $(window).width(); 
            if(savePosition && savePosition != 'undefined' && width > 1366){
                var top = (savePosition.top > 0 && savePosition.top < 1000)? savePosition.top : 0;
                var left = (savePosition.left > 0 && savePosition.left < 1950) ? savePosition.left : 0;
                jQuery('#dialogDetailValue').css({'top': top + 'px','left': left + 'px', 'z-index':'99999'});
            } else {
                var hei = jQuery('#dialogDetailValue').height();
                if(hei < 600){
                    jQuery('#dialogDetailValue').css({'top':"5%",'left':'0', 'right': '0', 'z-index':'99999'});
                } else {
                    jQuery('#dialogDetailValue').css({'top':"5%",'left':'15%', 'z-index':'99999'});
                }
                // console.log(width);
                jQuery('#wd-container-main').css({'width': width});
                $(window).resize(function(){
                    var width  = $(window).width(); 
                    jQuery('#wd-container-main').css({'width': width});
                });
            }
            try {
                jQuery(document).ready(init);
            } catch(e){
                console.log(e);
            }
            $(document).ready(function(){
                var saving = false;
                refreshMap(true);
            });
            $('#budget_db').jqxChart(settings);
            if( yourFormFilter['weather'] == 1 && $showKpiBudget ){
                var svgString1 = new XMLSerializer().serializeToString(document.querySelector('#svg_kpi_1'));
                $('#svg_kpi_1').css('margin-top', '40px');
                var canvas1 = document.getElementById("canvas_kpi");
                var ctx1 = canvas1.getContext("2d");
                var DOMURL1 = self.URL || self.webkitURL || self;
                var img1 = new Image();
                img1.crossOrigin = "Anonymous";
                var svg1 = new Blob([svgString1], {type: "image/svg+xml;charset=utf-8"});
                var url1 = DOMURL1.createObjectURL(svg1);
                img1.src = url1;
                img1.onload = function() {
                    try{
                        ctx1.drawImage(img1, 0, 0);
                        var png1 = canvas1.toDataURL("image/png");
                        document.querySelector('#png-container_kpi').innerHTML = '<img class="img_budget_export" style="display: none; width: 270px;float: left;height: 140px;margin:0; margin-top: 50px" src="'+png1+'"/>';
                        DOMURL1.revokeObjectURL(png1);
                    }catch(e){
                        console.log(e);
                    }
                };
                setTimeout(function(){
                    $('.wd-table').find('#svgChart').each(function(val, index){
                        var type = $(index).closest('div').data('type');
                        var svgString = new XMLSerializer().serializeToString(index);
                        var canvas = document.getElementById("canvas_" + type);
                        canvas.width = 900;
                        canvas.height = 300;
                        var ctx = canvas.getContext("2d");
                        var DOMURL = self.URL || self.webkitURL || self;
                        var img = new Image();
                        img.width = 900;
                        img.height = 300;
                        img.crossOrigin = "Anonymous";
                        var svg = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
                        var url = DOMURL.createObjectURL(svg);
                        img.src = url;
                        img.onload = function() {
                            try{
                                ctx.drawImage(img, 0, 0);
                                var png = canvas.toDataURL("image/png");
                                var style = 'display: none; width: 860px;float: left;height: 280px;margin:0; margin-left: 270px';
                                if(type == 'budget'){
                                    style = 'display: none; width: 860px;float: left;height: 280px;margin:0;';
                                }
                                document.querySelector('#png-container_' + type).innerHTML = '<img class="img_budget_export" style="'+style+'" src="'+png+'"/>';
                                DOMURL.revokeObjectURL(png);
                            }catch(e){
                                console.log(e);
                            }
                        };
                    });
                }, 2000);
            }
            setTimeout(function(){
                $(window).resize();
            }, 2000);
        }
    });
}

function refreshMap(show){
    var query = $.trim($('#coord-input').val());
    if( query ){
        //initial google maps
        $('#map-frame').prop('src', 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(query) + '&key=' + gapi);
        if( show ){
            $('#map-frame').show();
            $('#local-frame').hide();
            state = 0;
        }
    } else {
        $('#map-frame').prop('src', 'about:blank');
    }
}
$('.open-popup').each(function(index, value){
    var img = $(value).find('img');
    img.on('load', function(){
        var _height = $(this).height();
        if(_height && _height !== undefined && _height > 0 && _height < 252){
            $(img).css('padding-top', (252 - _height)/2 + 'px');
        }
    });
});
$('#CategoryCategory').change(function(){
    location.href = '<?php echo $this->Html->url('/projects/index_plus_preview/') ?>' + $(this).val() +'?view=new';
});

function openFilter(){
    var project_filter = $('.open-filter-form').closest('.wd-project-filter');
    project_filter.find('.search-filter').toggleClass('active');
    $('body').find('.wd-project-admin').toggleClass('active');
}
$('.close-filter').click(function(){
    $(this).closest('.search-filter').toggleClass('active');
    $('body').find('.wd-project-admin').toggleClass('active');
});

(function($){
//     var _btn_loadmore = $('.loadmore');
//         if( _btn_loadmore.length === 0 ) return;
//         _btn_loadmore.on('click', function(e){
//             e.preventDefault();
//             var _parent_filter = $(this).closest('.onsky-course-filter');
//             var _course_grid = _parent_filter.find('.kit-course-filter');
//             var data = {},
//             _this = $(this);
//             data.sort = _this.data('sort');
//             data.args = _parent_filter.find('.kit-filter-param').data('args'),
//             data.atts = _parent_filter.find('.kit-filter-param').data('atts');
//             data.columns = _parent_filter.find('.kit-filter-param').data('columns');
        
//             _course_grid.addClass('project-loading');
//             $.ajax({
//                 type: "POST",
//                 url: variable_js.ajax_url,
//                 data: {action: 'kite_theme_update_course', data },
//                 success: function(data){
//                     data = $.parseJSON( data );
//                     console.log(data);
//                     if(data.content){
//                         _course_grid.empty().append(data.content);
//                     }
//                     _course_grid.removeClass('course-loading');
                
//                 }
//             })
//         })
//     })
    var appstatus = <?php echo json_encode($appstatus);?>,
    personDefault = <?php echo json_encode($personDefault);?>,
    cate_id = appstatus ? appstatus : 1;
    function listProjectStautus(id, view_id){
        if(id != ''){
            $.ajax({
                url: '/projects/getPersonalizedViews/' + id,
                async: false,
                beforeSend: function(){
                    $('#CategoryStatus').html('Please waiting...');
                },
                success:function(datas) {
                    var datas = JSON.parse(datas);
                    var selected = selectDefined = selectDefault = '';
                    if(view_id != null){
                        if(view_id == 0){
                            selected = 'selected="selected"';
                        } else if(view_id == -1){
                            selectDefined = 'selected="selected"';
                        } else if(view_id == -2){
                            selectDefault = 'selected="selected"';
                        }
                    }
                    var content = '<option value="0" ' + selected + '><?php echo  __("------- Select -------", true);?></option>';
                    if(personDefault == false){
                        content += '<option value="-1" ' + selectDefined + '><?php echo  __("-- Default", true);?></option>';
                    } else {
                        content += '<option value="-2" ' + selectDefault + '><?php echo  __("-- Default", true);?></option>';
                    }
                    $.each(datas, function(ind, val){
                        var selected = '';
                        if(view_id == ind && view_id != null && view_id != -2 && view_id != -1 && view_id != 0){
                            selected = 'selected="selected"';
                        }
                        content += '<option value="' +ind+ '" ' + selected + '>' + val + '</option>';
                    });
                    $('#CategoryStatus').html(content);
                }
            });
        }
    }
    listProjectStautus(appstatus, null);
})(jQuery);
</script>
