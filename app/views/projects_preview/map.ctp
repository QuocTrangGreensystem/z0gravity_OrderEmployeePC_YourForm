<?php
$gapi = GMapAPISetting::getGAPI();
echo $this->element('dialog_detail_value');
echo $html->css('projects');
echo $html->css('preview/projects_map');
App::import('Model', 'Project');
App::import('Model', 'Company');

function _getPath($project_id) {
    $Project = new Project();
    $Company = new Company();
    $company = $Project->find('first', array(
        'recursive' => 0,
        'fields' => array('Company.parent_id', 'Company.company_name', 'Company.dir'),
        'conditions' => array('Project.id' => $project_id)
    ));
    $pcompany = $Company->find('first', array(
        'recursive' => -1,
        'conditions' => array('Company.id' => $company['Company']['parent_id'])
    ));
    $path = FILES . 'projects' . DS . 'globalviews' . DS;
    if ($pcompany) {
        $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
    }
    $path .= $company['Company']['dir'] . DS;
    return $path;
}
?>

<?php

// $_linkDashboard = '';
$checkScreen = '';
$_linkDashboard = $_title = '';
if( !empty($screenDashboard)){
	$checkScreen = '';
	/*
	* Kiem tra man hinh Indi hay YourForm+ co dang active
	*/
	if( isset($screenDashboard['indicator']['functions'])){
			$checkScreen = $screenDashboard['indicator']['functions'];
		} else {
			$checkScreen = $screenDashboard['your_form_plus']['functions'];
		}
	/*
	* Vong foreach de lay ten dinh nghia trong admin
	*/
	foreach($screenDashboard as $screen => $screen_val){
		$_linkDashboard = array();
		$_lang = $employee_info['Employee']['language'];
		$_title = ($_lang == 'fr') ? $screen_val['name_fre'] : (($_lang == 'en') ? $screen_val['name_eng'] :  __('Dashboard', true));
		if($screen == 'indicator') break; //true is pause foreach
	}
		
} else {
	$checkScreen = '';
}

$types = array(
    1 => __('In progress', true),
    2 => __('Opportunity', true),
    3 => __('Archived', true),
    4 => __('Model', true)
);
         $appstatus = $this->Session->read("App.status");
?>
<style>
    .wd-main-content{
        padding: 30px 40px 40px 40px !important;
    }
    .wd-main-content .wd-title{
        overflow: visible;
    }
    .wd-dropdown form {
        padding: 15px;
    }
    .wd-custom-checkbox *{
        box-sizing: border-box;
    }
    .list-type-project ul.list{
        max-height: 500px;
        overflow-y: auto;
        margin-bottom: 10px;
    }
    .filter {
        height: 40px;
        width: 150px;
        border: 1px solid #E0E6E8;
        padding: 0 20px 0 10px;
        color: #666666;
        font-family: "Open Sans";
        font-size: 14px;
        line-height: 38px;
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        -o-appearance: none;
        appearance: none;
        background: url(/img/new-icon/down.png) no-repeat right 10px center #fff !important;
    }
	button#btnSave {
		margin-left: 0px;
	}
	a#reset_button {
		margin-right: 0px;
	}
</style>

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-title">
                <div class="heading-back"><a href="javascript:window.history.back();" ><i class="icon-arrow-left"></i><span><?php __('Back');?></span></a></div>
                <select class="filter" id="category">
                    <option value="1"><?php echo  __("In progress", true)?></option>
                    <option value="2"><?php echo  __("Opportunity", true)?></option>
                    <option value="5"><?php echo  __("In progress + Opportunity", true)?></option>
                    <option value="3"><?php echo  __("Archived", true)?></option>
                    <option value="4"><?php echo  __("Model", true)?></option>
                </select>
                <div class="list-type-project">
                    <div class="wd-dropdown">
                        <span class="selected">
							<?php
								if (empty($prog) ){
									__("Type de projet");
								} else{
									$_selected = array();
									foreach ($listProgramFields as $key => $value){
										if( in_array($key, $prog) ) $_selected[] = $value;
									}
									echo implode( ', ', $_selected);
								}
							?>
                        </span>
                        <span class="wd-caret"></span>
                        <div class="popup-dropdown">
							<?php 
							echo $this->Form->create('typeProject', array('id' => 'typeProjectForm', 'url' => array('controller' => $this->params['controller'], 'action' => $this->params['action'])) );
							?>
                            <ul class="list">
							<?php 
							foreach ($listProgramFields as $key => $value){
								echo '<li>';
                                ?>
                                <div class="wd-custom-checkbox">
                                    <label> 
                                        <input type="checkbox" class="checkbox" id="typeProjectProjectAmrProgram<?php echo $key;?>" name="data[Project][typeProject][]" <?php if(in_array($key, $prog)) echo 'checked=checked';?> value="<?php echo $key;?>"/>
                                        <span class="wd-checkbox"></span>
                                        <span><?php echo $value;?></span>
                                    </label>
                                    </div>
                                <?php 
								echo '</li>';
							}
							?>
                            </ul>
							<input type="hidden" value="<?php echo $cat;?>" name="data[Project][cate]" id="curentCate">
                            <div class="wd-submit">
                                <button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave"> <span><?php __('Save'); ?></span>
                                </button>
                                <a class="btn-form-action btn-cancel cancel" id="reset_button" href="javascript:void(0)" onclick="javascript:cancel_dropdown(this);" title="<?php echo __("Cancel", true); ?>"><span><?php __('Cancel'); ?></span></a>
                            </div>
							<?php echo $this->Form->end();?>
                        </div>
                    </div>
                </div>
                <a href="javascript:;" onclick="expandScreen();" class="btn btn-expand hide-on-mobile"></a>
            </div>
            <div class="wd-list-project clearfix">

                <?php
                foreach ($projects as $key => $project) {
                    $projects[$key]['Project']['color'] = !empty($project['Project']['project_amr_program_id']) && !empty($projectAmrProgram[ $project['Project']['project_amr_program_id'] ]) ? $projectAmrProgram[ $project['Project']['project_amr_program_id'] ] : '#004380';
                    $projects[$key]['Project']['images'] = !empty($imageGlobals[$project['Project']['id']]) ? $imageGlobals[$project['Project']['id']] : '';
                    $projects[$key]['Project']['weather'] = !empty($listWeather[$project['Project']['id']]) ? $listWeather[$project['Project']['id']] : 'sun';
                    $projects[$key]['Project']['rank'] = !empty($listRank[$project['Project']['id']]) ? $listRank[$project['Project']['id']] : 'up';
                    // lay link image.
                     $link = '/img/project_preview_default2x.png'; // default
					if (!empty($imageGlobals[$project['Project']['id']])) {
                        $view = $imageGlobals[$project['Project']['id']];
                        if (preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $view) && file_exists(_getPath($project['Project']['id']) . $view)) {
                            $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project['Project']['id'], '?' => array('sid' => $api_key)), true);
                        }
                    }
                    $projects[$key]['Project']['link'] = $link;
                }
                ?>
                <div style="clear: both"></div>
                <div id="map"></div>
                <div class="list-container">
                    <ul id="list">
						<?php foreach($projects as $key => $project):
							$p = $project['Project'];
							$_color = $p['color'];
							$icon = $p['latlng'] ? $p['category'] : 0;
						?>
                        <li class="<?php echo empty($p['address']) ? 'disable' : 'enable'; ?>">
                                <!-- <a href="<?php echo $html->url(array('controller'=> $ACLController, 'action' => $ACLAction, $p['id']));?>" target="_blank" onclick="gotoMarker(this)" data-pid="<?php echo $p['id'] ?>"> -->
                            <a href="<?php echo $html->url(array('controller'=> $ACLController, 'action' => $ACLAction, $p['id']));?>" target="_blank" data-pid="<?php echo $p['id'] ?>"<?php if(empty($p['address'])) { ?> title="<?php __('No location has been set');?>" <?php } ?>>
                                <span class ="location-map" style="background-color: <?php echo $_color ?>"><?php echo $key+1 ?></span>
                                <span class="project-name"><?php echo $p['project_name'] ?></span>
                            </a>
                        </li>
						<?php endforeach;
						?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gapi ?>&amp;v=3.exp"></script>
<script type="text/javascript">
    var gapi = <?php echo json_encode($gapi) ?>,
            projects = <?php echo json_encode($projects) ?>,
            checkScreen = <?php echo json_encode($checkScreen) ?>,
            mapDiv = document.getElementById('map'),
            counter,
            map, max = 0, markers = {};
    function set_content_height() {
        var _content = $('#list, #map');
        _content.css({
            height: $(window).height() - _content.offset().top - 40,
        });
    }
    set_content_height();
    $(window).resize(function () {
        set_content_height();
    });
    //Define marker object
    function SexyMarker(latlng, args) {
        this.latlng = latlng;
        this.args = args;
    }
    //inherit the overlay object
    SexyMarker.prototype = new google.maps.OverlayView();
    SexyMarker.prototype.draw = function () {
        // console.log(checkScreen);
        var self = this;
        var div = this.div;
        var checkS = '';
        if (checkScreen == 'indicator') {
            checkS = '<a href="<?php echo $html->url(array('controller'=> 'project_amrs_preview', 'action' => 'indicator'));?>' + '/' + this.args.marker_id + '"><img title="<?php echo $_title;?>" src="<?php echo $html->url('/img/new-icon/dashboard.png'); ?>"></a>';
        } else if (checkScreen == 'your_form_plus') {
            checkS = '<a href="<?php echo $html->url(array('controller'=> 'projects', 'action' => 'your_form_plus'));?>' + '/' + this.args.marker_id + '"><img title="<?php echo $_title;?>" src="<?php echo $html->url('/img/new-icon/dashboard.png'); ?>"></a>';
        }
        if (!div) {
            div = this.div = document.createElement('div');
            div.className = 'sexy-marker ' + (this.args.marker_id ? 'sexy-marker-' + this.args.marker_id : '');
            // div.style.zIndex = this.args.z ? this.args.z : 1;
            if (this.args.link !== undefined && this.args.link != '') {
                div.innerHTML = '<div style="background: ' + this.args.color + '" class="location-map"><a target="_blank" href="<?php echo $html->url('/projects/your_form/') ?>' + this.args.marker_id + '">' + (this.args.z + 1) + '</a></div>' +
                        '<div class="sexy-marker-content">' +
                        '<div class="sexy-maker-images" id="sexy-maker-images-' + this.args.marker_id + '"><a target="_blank" href="<?php echo $html->url('/project_global_views/index/') ?>' + this.args.marker_id + '"><img src="' + this.args.link + '"></a></div>' +
                        '<div class="sexy-maker-icon has-image">' +
                        '<div class="sexy-maker-weather"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>' + this.args.marker_id + '"><img src="<?php echo $html->url('/img/new-icon/') ?>' + this.args.weather + '.png"></a></div>' +
                        '<div class="sexy-maker-rank"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>' + this.args.marker_id + '"><img src="<?php echo $html->url('/img/new-icon/project_rank/') ?>' + this.args.rank + '.png"></a></div>' +
                        '</div>' +
                        '<div class="sexy-maker-name">' +
                        '<a target="_blank" style="color: #000;font-weight: normal;" href="<?php echo $html->url(array('controller'=> $ACLController, 'action' => $ACLAction));?>' + '/' + this.args.marker_id + '">' + this.args.title + '</a>' +
                        '<div class="link_to">' +
                        checkS
                        +
                        '<a href="<?php echo $html->url(array('controller'=> $ACLController, 'action' => $ACLAction));?>' + '/' + this.args.marker_id + '"><img title="<?php __('Open project');?>" src="<?php echo $html->url('/img/new-icon/screen-admin.png');?>"></a>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
            } else {
                div.innerHTML = '<div style="background: ' + this.args.color + '" class="location-map"><a target="_blank" href="<?php echo $html->url('/projects/your_form/') ?>' + this.args.marker_id + '">' + (this.args.z + 1) + '</a></div>' +
                        '<div class="sexy-marker-content">' +
                        '<div class="sexy-maker-icon no-image">' +
                        '<div class="sexy-maker-weather"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>' + this.args.marker_id + '"><img src="<?php echo $html->url('/img/') ?>' + this.args.weather + '.svg"></a></div>' +
                        '<div class="sexy-maker-rank"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>' + this.args.marker_id + '"><img src="<?php echo $html->url('/img/') ?>' + this.args.rank + '.svg"></a></div>' +
                        '</div>' +
                        '<div class="sexy-maker-name">' +
                        '<a target="_blank" style="color: #000;font-weight: normal;" href="<?php echo $html->url(array('controller'=> $ACLController, 'action' => $ACLAction));?>' + '/' + this.args.marker_id + '">' + this.args.title + '</a>' +
                        '<div class="link_to">' +
                        '<a href="<?php echo $html->url(array('controller'=> 'project_amrs_preview', 'action' => 'indicator'));?>' + '/' + this.args.marker_id + '"><img title="Dashboard" src="<?php echo $html->url('/img/new-icon/dashboard.png'); ?>"></a>' +
                        '<a href="<?php echo $html->url(array('controller'=> $ACLController, 'action' => $ACLAction));?>' + '/' + this.args.marker_id + '"><img title="<?php __('Open project');?>" src="<?php echo $html->url('/img/new-icon/screen-admin.png'); ?>"></a>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
            }
            if (typeof (self.args.marker_id) !== 'undefined') {
                if (typeof div.dataset != 'undefined') {
                    div.dataset.marker_id = self.args.marker_id;
                } else {
                    $(div).data('marker_id', self.args.marker_id);
                }
            }
            google.maps.event.addDomListener(div, "click", function (event) {
                self.pop();
                event.stopPropagation();
                //google.maps.event.trigger(self, "click");
            });
            var panes = this.getPanes();
            panes.overlayImage.appendChild(div);
        }
        this.reposition();
    };

    SexyMarker.prototype.reposition = function () {
        var div = this.div;
        var point = this.getProjection().fromLatLngToDivPixel(this.latlng);
        if (point) {
            div.style.left = (point.x - 15) + 'px';
            div.style.top = (point.y - div.clientHeight) + 'px';
        }
    }

    SexyMarker.prototype.remove = function () {
        if (this.div) {
            this.div.parentNode.removeChild(this.div);
            this.div = null;
        }
    };

    SexyMarker.prototype.getPosition = function () {
        return this.latlng;
    };

    SexyMarker.prototype.setZ = function (z) {
        this.div.style.zIndex = z;
    };

    SexyMarker.prototype.resetZ = function () {
        this.setZ(this.args.z ? this.args.z : 1);
    };

    SexyMarker.prototype.pop = function () {
        this.setZ(++max);
        this.args.z = max;
    }

    //run the code
    function initMap() {
        //iterate each projects
        $.each(projects, function (i, project) {
            project = project.Project;
            if (project.latlng) {
                //format number
                var latlng = $.parseJSON(project.latlng);
                latlng.lat = parseFloat(latlng.lat);
                latlng.lng = parseFloat(latlng.lng);
                //create latlng object
                latlng = new google.maps.LatLng(latlng.lat, latlng.lng);
                //create marker
                var marker = new SexyMarker(latlng, {
                    title: project.project_name,
                    marker_id: project.id,
                    z: i,
                    // classes: 'color-' + project.category,
                    color: project.color,
                    weather: project.weather,
                    rank: project.rank,
                    images: project.images,
                    link: project.link
                });
                markers[project.id] = marker;
                max = i + 1;
            }
        });
        // console.log(markers);
        if (!$.isEmptyObject(markers)) {
            map = new google.maps.Map(mapDiv, {
                zoom: 13
            });
            $.each(markers, function (id, marker) {
                marker.setMap(map);
                map.setCenter(marker.getPosition());
            });
        }
        // else{
        // console.log( 'markers is empty');
        // }
    }
    google.maps.event.addDomListener(window, 'load', initMap);
    $(document).ready(function () {
        var _cat = <?php echo json_encode($cat) ?>;
        if (_cat[0] != 'p') {
            $('#category').val(_cat);
        }
        $('#category').change(function () {
			var cate = $(this).val();
			$('#curentCate').val(cate).closest('form').submit();
            // location.href = '<?php echo $this->Html->url('/projects_preview/map/') ?>' + $(this).val();
        });
        //click on marker on project list -> setCenter, set zIndex
        $('#list li a').click(function (e) {
            e.preventDefault();
            var id = $(this).data('pid'),
                    marker = markers[id];
            $('#list li').removeClass('selected');
            if ($(this).closest('li').hasClass('enable')) {
                $(this).closest('li').addClass('selected');
            }
            try {
                marker.pop();
                map.panTo(marker.getPosition());
            } catch (ex) {
                console.log('Error!');
            }
        });
    });

    function expandScreen() {
        $('#collapse').show();
        $('#table-control').hide();
        $('#title').hide();
        $('#list').hide();
        $('#map').addClass('fullScreen');
        $('#hint').addClass('hint-full-screen');
        google.maps.event.trigger(map, "resize");
        $(window).resize();
    }
    function collapseScreen() {
        $('#table-control').show();
        $('#title').show();
        $('#list').show();
        $('#collapse').hide();
        $('#map').removeClass('fullScreen');
        $('#hint').removeClass('hint-full-screen');
        google.maps.event.trigger(map, "resize");
        $(window).resize();
    }
    function showProjectGlobalViews(element, id) {
        var seconds = 0; // uptime in seconds
        var check = false;
        $('#sexy-maker-images-' + id).addClass('pch_loading');
        var timer = setInterval(function () {
            seconds++;
            if (seconds >= 1) {
                check = true;
                clearInterval(timer);
                jQuery.ajax({
                    url: "/project_global_views/ajax/" + id + "/map",
                    type: "GET",
                    cache: false,
                    success: function (html) {
                        var dump = $('<div />').append(html);
                        if (dump.children('.error').length == 1) {
                            //do nothing
                        } else if (dump.children('#attachment-type').val()) {
                            jQuery('#dialogDetailValue').css({'top': '25%', 'left': '32%'});
                            jQuery('#dialogDetailValue').css({'padding-top': 0, 'z-index': 9999});
                            jQuery('#contentDialog').html(html);
                            showMe();
                        }
                        $('#sexy-maker-images-' + id).removeClass('pch_loading');
                    }
                });
            }
        }, 800);
        element.onmouseout = function () {
            seconds = 0;
            if (!check)
                $('#sexy-maker-images-' + id).removeClass('pch_loading');
            clearInterval(timer);
        }
    }

    $('.list-container').resizable({
        handles: "w",
        maxWidth: $('.wd-list-project').width() / 2,
        minWidth: Math.min(250, $('.wd-list-project').width() / 2),
        resize: function (e, ui) {
            // alert('resize');
            var _map_width = $('.wd-main-content').width() - $('.list-container').width();
            console.log(_map_width);
            $('#map').width(_map_width);


        }
    });
</script>
