<?php
echo $html->script(array(
    'jquery.validation.min',
    'html2canvas',
    'jquery.html2canvas.yourform',
    'vis.min',
    'dashboard/jqx-all',
    'dashboard/jqxchart',
    'masonry.pkgd.min.js',
    'draw-progress'
));
echo $html->css(array(
    'gantt_v2_1',
    'business',
    'vis.min',
    // '3-col-portfolio',    
    'preview/grid-project',
));
echo $this->element('dialog_detail_value');
echo $this->element('dialog_projects');
?>
<style>
    body #dialog_my_assistant{
        transform: translateX(-46%);
    }
    .nav-preview__item .nav-account{
        width: 180px;
    }
    @media(max-width: 1199px){
            
        .wd-project-filter .open-filter-form img{
            margin-top: 0;
        }
    }
    .multiselect{
        width: 100%;
    }
</style>
<?php 
function draw_line_progress($value){
    $_html = '';
    $_color_gray = '#E2E6E8';
    $_color_green = array('#6EAF79', '#89BB92', '#AACCB0', '#C1D6C5', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9' );
    $_color_blue =  array('#6FB0CF', '#87BFDA', '#A3CCE0', '#BBDAE9', '#D6E8F0');
    $_use_color = $value > 50 ? $_color_green : $_color_blue;
    $_index = 1; $_current_color = '';
    for( $_index = 1; $_index <= 10; $_index++){
        $_current_color = $_index*10 <= $value ? $_use_color[(intval($value/10) - $_index)] : $_color_gray;
        $_html .= '<span class="progress-node" style="background: ' . $_current_color . '"></span>';
    }
    return $_html;
}
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
        <?php if(!empty($listProgramFields)){?>
                <select class="wd-customs" id="project-program" rel="no-history">
                    <option value=""><?php echo  __("Type de projet", true)?></option>
                   <?php  foreach ($listProgramFields as $key => $value) {?>
                        <option <?php if(!empty($prog) && $prog == $key) echo 'selected="selected" '; ?> value="<?php echo $key ?>"><?php echo $value; ?></option>
                   <?php  } ?>
                </select>
            <?php } ?>
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

                        <option <?php if(!empty($prog) && $prog == $key) echo 'selected="selected" '; ?> value="<?php echo $key ?>"><?php echo $value; ?></option>
                   <?php  } ?>
                </select>
            <?php } ?>
            <input id = 'project-name' type="text" name="project-name" placeholder="Project">
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
            
            <input id = 'avancement' type="text" name="avancement" placeholder="Avancement">

            <a class="search-button"><img title="header-bottom"  src="<?php echo $html->url('/img/new-icon/search.png'); ?>"/></a>
        <?php
        echo $this->Form->end();
    ?>
    </div>
</div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout wd-project-grid">
        <div class="container-portfolio">

            <?php
             $i = 0; foreach ($listProjects as $listProject) { ?>
                <?php foreach ($listProject as $project) { ?>
                    <div class="portfolio-item portfolio-item-<?php  echo $i++ ?>">
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
                        <div class="open-popup" data-id="<?php echo $project['Project']['id'] ?>">
                            <?php 
                                if(!empty($link)){ ?>
                                    <img src="<?php echo (!empty($link) ? $link : '') ?>" alt="">
                                <?php }else{
                                    if(!empty($projectGloView[$project['Project']['id']]) && $projectGloView[$project['Project']['id']]['is_file'] == 0 && $projectGloView[$project['Project']['id']]['is_https'] == 0){ ?>
                                        <div class="widget-content"><?php echo $projectGloView[$project['Project']['id']]['attachment']; ?></div>
                                    <?php }
                                }
                            ?>
                            
                        </div>
                        <?php }else{ ?>
                        <div class="">
                            <iframe src="<?php echo $link; ?>" class="img-responsive" style="width: 100%;height: 252px;"></iframe>
                            <div data-id="<?php echo $project['Project']['id'] ?>"  class="overlay"></div>
                        </div>
                        <?php } ?>
                        <div class="project-item-content">
                            <?php 
                                $full_name = $name = '';
								if ( !empty($listProjectManager[$project['Project']['id']])){
									$full_name = trim($listProjectManager[$project['Project']['id']]['first_name']) .' '.trim($listProjectManager[$project['Project']['id']]['last_name']);
									$name = substr( trim($listProjectManager[$project['Project']['id']]['first_name']),  0, 1) .''.substr( trim($listProjectManager[$project['Project']['id']]['last_name']),  0, 1);
								}
                                // $linkProjectName = $html->url('/' . $ACLController . '/' . $ACLAction);
                            ?>
                            <a title ="<?php echo $full_name; ?>"><span class="project-manager-name circle-name"><?php echo $name; ?></span></a>
                            <div class="text-ellipsis">
                                <a href="<?php echo $html->url(array( 'controller' => $ACLController , 'action'=> $ACLAction, $project['Project']['id'])); ?>" target="_blank"><?php echo $project['Project']['project_name'] ?></a>
                            </div>

                            <?php 
                            $this_description = '';
                            if( !empty( $logs[ $project['Project']['id'] ]) ) $this_description = $logs[ $project['Project']['id'] ]['ProjectAmr']['description']; ?>
                            <p class="project-description"><?php echo $this_description ? $this_description : '' ?></p>
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
                                        <li><a href="<?php echo $html->url(array('controller' => 'project_amrs_preview', 'action' => 'indicator' , $project['Project']['id']));?>?view=new"><img title="Dashboard" src="<?php echo $html->url('/img/new-icon/dashboard.png'); ?>"/></a></li>
                                        <li><a href="<?php echo $html->url('/' . $ACLController . '/' . $ACLAction . '/' . $project['Project']['id']); ?>"><img title="<?php __('Open project');?>" src="<?php echo $html->url('/img/new-icon/screen-admin.png'); ?>"/></a></li>
                                    </ul>
                                    <div class="project-progress">
                                        <p class="progress-full"> <?php echo draw_line_progress($initDate);?> </p>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </div>
                        
                    </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <a style="display: none" class="loadmore" href="#"><img title="Load more" src="<?php echo $html->url('/img/new-icon/down.png'); ?>"/></a>
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
    // var img = $(value).find('img');
    // img.on('load', function(){
    //     var _height = $(this).height();
    //     if(_height && _height !== undefined && _height > 0 && _height < 252){
    //         $(img).css('padding-top', (252 - _height)/2 + 'px');
    //     }
    // });
});
$('#CategoryCategory').change(function(){
    location.href = '<?php echo $this->Html->url('/projects_preview/index_plus_preview/') ?>' + $(this).val() +'?view=new';
});
$('#project-program').change(function(){
    location.href = '<?php echo $this->Html->url('/projects_preview/index_plus_preview/') ?>p-' + $(this).val() +'?view=new';
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
    var search_button = $('.search-button');
    if( search_button.length === 0 ) return;
    search_button.on('click', function(e){
        e.preventDefault();
        var form = $(this).closest('#FilterIndexPlusPreviewForm');
        var prog = form.find('#project-program').val();
        var weather = form.find('#weather').val();
        var project_manager = form.find('#project-manager').val();
        var project_name = form.find('#project-name').val();
        var avancement = form.find('#avancement').val();

        var param = 'filter-';
        if(prog) param += 'prog_'+prog;
        if(weather) param += '-weather_'+weather;
        if(project_manager) param += '-pm_'+project_manager;
        if(avancement) param += '-avancement_'+avancement;
        if(project_name) param += '-pname_'+project_name;
        location.href = '<?php echo $this->Html->url('/projects_preview/index_plus_preview/')?>' + param +'?view=new';

    });
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
    $(window).load(function() {
        var n = <?php echo $i ?>;
        if(n > 0){
            for( i = 0 ; i < n; i++){
                var height = $('.portfolio-item-'+ i).height();
                $('.portfolio-item-'+ i).height(height);
            }
        }
        $('.container-portfolio').masonry({
          // options
          itemSelector: '.portfolio-item',
          columnWidth: 310,
          gutter: 30,
          columnWidth: '.portfolio-item',
          percentPosition: true
        });
    });
})(jQuery);
</script>
