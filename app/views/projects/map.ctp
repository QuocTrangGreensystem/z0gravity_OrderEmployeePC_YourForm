<?php
$gapi = GMapAPISetting::getGAPI();
echo $this->element('dialog_detail_value');
echo $html->css('projects');
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
<style>
    #map {
        width: 75%;
        height: 650px;
        border: 1px solid #D8D8D8;
        float: right;
    }
    .sexy-marker {
        position: absolute;
        min-width: 150px;
        padding: 5px 8px;
        text-align: center;
        font-size: 13px;
        color: #fff;
        border-radius: 4px;
    }
    /*.sexy-marker:after, .sexy-marker:before {
        top: 100%;
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }*/
    /*.sexy-marker:after {
        border-width: 5px;
        margin-left: -5px;
    }
    .sexy-marker:before {
        border-width: 6px;
        margin-left: -6px;
    }*/
    /*.color-1 {
        background: #5c9e23;
        border: 1px solid #346b1d;
        color: #ddf8c6;
    }
    .color-1:before {
        border-color: rgba(52, 107, 29, 0);
        border-top-color: #346b1d;
    }
    .color-1:after {
        border-color: rgba(92, 158, 35, 0);
        border-top-color: #5c9e23;
    }
    .color-2 {
        background: #1391bf;
        border: 1px solid #145973;
    }
    .color-2:before {
        border-color: rgba(20, 89, 115, 0);
        border-top-color: #145973;
    }
    .color-2:after {
        border-color: rgba(19, 145, 191, 0);
        border-top-color: #1391bf;
    }
    .color-3 {
        background: #d55b42;
        border: 1px solid #bd321c;
    }
    .color-3:after {
        border-color: rgba(213, 91, 66, 0);
        border-top-color: #d55b42;
    }
    .color-3:before {
        border-color: rgba(194, 225, 245, 0);
        border-top-color: #bd321c;
    }

    .color-4 {
        background: #e0dcc5;
        border: 1px solid #8a8a8a;
        color: #000;
        text-shadow: 0 0 0 #fff;
    }
    .color-4:before {
        border-color: rgba(138, 138, 138, 0);
        border-top-color: #8a8a8a;
    }
    .color-4:after {
        border-color: rgba(224, 220, 197, 0);
        border-top-color: #e0dcc5;
    }*/
    #title {
        float: left;
        width: 24%;
        margin-bottom: 10px;
    }
    #list {
        width: 24%;
        float: left;
        height: 650px;
        overflow: auto;
        border: 1px solid #ddd;
    }
    #list li {
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }
    #list li:last-child {
        border: 0;
    }
    .marker-icon {
        margin-right: 5px;
        vertical-align: middle;
        cursor: pointer;
    }
    .marker-icon-0 {
        cursor: default;
    }
    #hint span {
        display: inline-block;
        margin-right: 20px;
    }
    .location-map{
        float: left;
        text-align: center;
        vertical-align: middle;
        margin-top: -5px;
        width: 26px;
        height: 26px;
        overflow: hidden;
        border-bottom-right-radius: 50%;
        border-bottom-left-radius: 50%;
        border-top-left-radius: 50%;
        -webkit-transform:rotate(135deg);
        transform: rotate(135deg);
        z-index: 99;
        border: 1px solid #add;
    }
    .location-map a, .location-map span{
        -webkit-transform:rotate(-135deg);
        transform: rotate(-135deg);
        position: absolute;
        margin-top: 5px;
        margin-left: -7px;
        font-size: 14px;
        color: #fff;
    }
    .sexy-marker .location-map{
        width: 35px;
        height: 35px;
        margin-left: -10px;
        border: 1px solid #add;
    }
    .sexy-marker .location-map a, .sexy-marker .location-map span{
        margin-top: 8px;
        margin-left: -5px;
        text-align: center;
        font-size: 14px;
    }
    .sexy-marker-content{
        background: #fff;
        color: #000;
        border-radius: 10px;
        box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.3);
        border-color: rgba(52, 107, 29, 0);
        border-top-left-radius: 20px;
        border-bottom-left-radius: 13px;
    }
    .sexy-maker-name{
        display: none;
        margin-top: -5px;
        height: 20px;
        white-space: nowrap;
        margin-left: 28px;
    }
    .sexy-maker-weather img{
        width: 23px;
        height: 23px;
    }
    .sexy-maker-rank img{
        width: 23px;
        height: 23px;
    }
    .sexy-maker-bot{
        clear: both;
        display: none;
        height: 20px;
    }
    .sexy-maker-images img{
        width: 23px;
        height: 23px;
    }
    .sexy-maker-images img:hover, .sexy-maker-rank img:hover, .sexy-maker-weather img:hover{
        cursor: pointer;
    }
    .sexy-maker-weather, .sexy-maker-rank, .sexy-maker-images{
        width: 30px;
        display: block;
        float: left;
    }
    .hint-full-screen{
        background: #fff;
        z-index: 1000;
        float: right;
        width: 195px;
        margin-top: -194px;
        opacity: 1;
        display: inline-block;
        position: absolute;
        right: 40px;
        height: 30px;
        padding-top: 10px;
    }
    #contentDialog{
        margin: 0;
        padding: 0;
    }
    .wd-tab .wd-panel{
        padding: 0px;
    }
    .wd-t2{
        margin-left: 40px;
    }
    #dialogDetailValue{
        min-width: 200px;
        max-width: 802px;
    }
    #contentDialog img{
        max-width: 800px;
        max-height: 600px;
    }
    @media all and (-ms-high-contrast: active), (-ms-high-contrast: none) {
        .sexy-marker .location-map a, .sexy-marker .location-map span{
            margin-left: 9px;
            margin-top: 9px;
        }
        .location-map a, .location-map span{
            margin-left: 5px;
            margin-top: 6px;
        }
        .sexy-maker-name{
            margin-left: 28px;
        }
    }
    @-moz-document url-prefix() {
        .sexy-maker-name {
            margin-top: -5px;
            margin-left: 30px;
        }
    }
</style>
<?php
$types = array(
    1 => __('In progress', true),
    2 => __('Opportunity', true),
    3 => __('Archived', true),
    4 => __('Model', true)
);
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div id="title">
                    <select style="margin-right:5px; padding: 6px; float: none" class="wd-customs" id="category">
                        <option value="1"><?php echo  __("In progress", true)?></option>
                        <option value="2"><?php echo  __("Opportunity", true)?></option>
                        <option value="5"><?php echo  __("In progress + Opportunity", true)?></option>
                        <option value="3"><?php echo  __("Archived", true)?></option>
                        <option value="4"><?php echo  __("Model", true)?></option>
                    </select>
                    <a href="<?php echo $this->Html->url('/projects/?cate=' . $cat) ?>" class="btn btn-back"><span>&nbsp;</span></a>
                    <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen"></a>
                </div>
                <div id="hint">
                    <input type="checkbox" id="check_name" value=""><label><?php echo __("Project name", true); ?></label>
                    <input type="checkbox" id="check_weather" value=""><label><?php echo __("Weather", true); ?></label>
                </div>
                <?php
                foreach ($projects as $key => $project) {
                    $projects[$key]['Project']['color'] = !empty($project['Project']['project_amr_program_id']) && !empty($projectAmrProgram[ $project['Project']['project_amr_program_id'] ]) ? $projectAmrProgram[ $project['Project']['project_amr_program_id'] ] : '#004380';
                    $projects[$key]['Project']['images'] = !empty($imageGlobals[$project['Project']['id']]) ? $imageGlobals[$project['Project']['id']] : '';
                    $projects[$key]['Project']['weather'] = !empty($listWeather[$project['Project']['id']]) ? $listWeather[$project['Project']['id']] : 'sun';
                    $projects[$key]['Project']['rank'] = !empty($listRank[$project['Project']['id']]) ? $listRank[$project['Project']['id']] : 'up';
                    // lay link image.
                    $link = $this->Html->url(array('controller' => 'project_global_views', 'action' => 'attachment', $project['Project']['id'], '?' => array('sid' => $api_key)), true);
                    if (!empty($imageGlobals[$project['Project']['id']])) {
                        $view = $imageGlobals[$project['Project']['id']];
                        if( $view['is_file'] == 1 && !file_exists(_getPath($project['Project']['id']) . $view) ){
                            $link = '';
                        } else if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $view)) {
                            $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                        }
                    } else {
                        $link = '';
                    }
                    $projects[$key]['Project']['link'] = $link;
                }
                ?>
                <div style="clear: both"></div>
                <ul id="list">
                    <?php foreach($projects as $key => $project):
                        $p = $project['Project'];
                        $_color = $p['color'];
                        $icon = $p['latlng'] ? $p['category'] : 0; ?>
                    <li style="clear: both; padding-bottom: 10px;">
                        <div class ="location-map" style="background-color: <?php echo $_color ?>"><span><?php echo $key+1 ?><span></div>
                        <a style="margin-left: 5px" href="<?php echo $this->Html->url('/projects/edit/' . $p['id']) ?>" target="_blank"><?php echo $p['project_name'] ?></a>
                    </li>
                    <?php endforeach ?>
                </ul>
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
<div id="collapse" onclick="collapseScreen();" ><button class="btn btn-esc"></button></div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gapi ?>&amp;v=3.exp"></script>
<script type="text/javascript">
    var gapi = <?php echo json_encode($gapi) ?>,
        projects = <?php echo json_encode($projects) ?>,
        mapDiv = document.getElementById('map'),
        counter,
        map, max = 0, markers = {};
    //Define marker object
    function SexyMarker(latlng, args) {
        this.latlng = latlng;
        this.args = args;
    }
    //inherit the overlay object
    SexyMarker.prototype = new google.maps.OverlayView();
    SexyMarker.prototype.draw = function() {
        var self = this;
        var div = this.div;
        if (!div) {
            div = this.div = document.createElement('div');
            div.className = 'sexy-marker ' + (this.args.marker_id ? 'sexy-marker-'+this.args.marker_id : '');
            div.style.zIndex = this.args.z ? this.args.z : 1;
            if(this.args.link !== undefined && this.args.link != ''){
                div.innerHTML = '<div style="background: '+this.args.color+'" class="location-map"><a target="_blank" href="<?php echo $html->url('/projects/your_form/') ?>'+this.args.marker_id+'">'+(this.args.z+1)+'</a></div>' +
                                '<div class="sexy-marker-content">' +
                                '<div class="sexy-maker-name"><a target="_blank" style="color: #000;font-weight: normal;" href="<?php echo $html->url('/projects/your_form/') ?>'+this.args.marker_id+'">'+this.args.title+'</a></div>' +
                                '<div class="sexy-maker-bot">' +
                                '<div class="sexy-maker-weather"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>'+this.args.marker_id+'"><img src="<?php echo $html->url('/img/') ?>' + this.args.weather + '.svg"></a></div>' +
                                '<div class="sexy-maker-rank"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>'+this.args.marker_id+'"><img src="<?php echo $html->url('/img/') ?>' + this.args.rank + '.svg"></a></div>' +
                                '<div class="sexy-maker-images" id="sexy-maker-images-'+this.args.marker_id+'"><a target="_blank" href="<?php echo $html->url('/project_global_views/index/') ?>'+this.args.marker_id+'"><img onmouseover="counter=setInterval(showProjectGlobalViews(this, '+this.args.marker_id+'), 1000)" onmouseout="clearInterval(counter);" src="' + this.args.link + '"></a></div>'+
                                '</div></div>';
            } else {
                div.innerHTML = '<div style="background: '+this.args.color+'" class="location-map"><a target="_blank" href="<?php echo $html->url('/projects/your_form/') ?>'+this.args.marker_id+'">'+(this.args.z+1)+'</a></div>' +
                                '<div class="sexy-marker-content">' +
                                '<div class="sexy-maker-name"><a target="_blank" style="color: #000;font-weight: normal;" href="<?php echo $html->url('/projects/your_form/') ?>'+this.args.marker_id+'">'+this.args.title+'</a></div>' +
                                '<div class="sexy-maker-bot">' +
                                '<div class="sexy-maker-weather"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>'+this.args.marker_id+'"><img src="<?php echo $html->url('/img/') ?>' + this.args.weather + '.svg"></a></div>' +
                                '<div class="sexy-maker-rank"><a target="_blank" href="<?php echo $html->url('/project_amrs/index_plus/') ?>'+this.args.marker_id+'"><img src="<?php echo $html->url('/img/') ?>' + this.args.rank + '.svg"></a></div>' +
                                '</div></div>';
            }
            if (typeof(self.args.marker_id) !== 'undefined' ) {
                if( typeof div.dataset != 'undefined' ){
                    div.dataset.marker_id = self.args.marker_id;
                } else {
                    $(div).data('marker_id', self.args.marker_id);
                }
            }
            google.maps.event.addDomListener(div, "click", function(event) {
                self.pop();
                event.stopPropagation();
                //google.maps.event.trigger(self, "click");
            });
            var panes = this.getPanes();
            panes.overlayImage.appendChild(div);
        }
        this.reposition();
    };

    SexyMarker.prototype.reposition = function(){
        var div = this.div;
        var point = this.getProjection().fromLatLngToDivPixel(this.latlng);
        if (point) {
            div.style.left = (point.x-15) + 'px';
            div.style.top = (point.y - div.clientHeight ) + 'px';
        }
    }

    SexyMarker.prototype.remove = function() {
        if (this.div) {
            this.div.parentNode.removeChild(this.div);
            this.div = null;
        }
    };

    SexyMarker.prototype.getPosition = function() {
        return this.latlng;
    };

    SexyMarker.prototype.setZ = function(z) {
        this.div.style.zIndex = z;
    };

    SexyMarker.prototype.resetZ = function() {
        this.setZ(this.args.z ? this.args.z : 1);
    };

    SexyMarker.prototype.pop = function(){
        this.setZ(++max);
        this.args.z = max;
    }

    //run the code
    function initMap(){
        //iterate each projects
        $.each(projects, function(i, project){
            project = project.Project;
            if( project.latlng ){
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
                max = i+1;
            }
        });
        if( !$.isEmptyObject(markers) ){
            map = new google.maps.Map(mapDiv, {
                zoom: 13
            });
            $.each(markers, function(id, marker){
                marker.setMap(map);
                map.setCenter(marker.getPosition());
            });

            // google.maps.event.addDomListener(mapDiv, 'click', function(event) {
            //     $.each(markers, function(i, marker){
            // 		marker.resetZ();
            // 	});
            // });
        }
    }
    google.maps.event.addDomListener(window, 'load', initMap);
    $(document).ready(function(){
		var _cat = <?php echo json_encode($cat) ?>;
		if( _cat[0] != 'p'){
			$('#category').val(_cat);
		}
		$('#category').change(function(){
            location.href = '<?php echo $this->Html->url('/projects/map/') ?>' + $(this).val();
        });
        //click on marker on project list -> setCenter, set zIndex
        $('.marker-icon:not(.marker-icon-0)').click(function(){
            var id = $(this).data('id'),
                marker = markers[id];
            try {
                marker.pop();
                map.panTo(marker.getPosition());
            } catch(ex){}
        });
    });
    $('#check_name').click(function(){
        if($('#check_name').is(":checked")){
            $('.sexy-maker-name').css('display', 'table');
        } else {
            $('.sexy-maker-name').css('display', 'none');
        }
    });
    $('#check_weather').click(function(){
        if($('#check_weather').is(":checked")){
            $('.sexy-maker-bot').css('display', 'inline-block');
        } else {
            $('.sexy-maker-bot').css('display', 'none');
        }
    });
    function expandScreen(){
        $('#collapse').show();
        $('#table-control').hide();
        $('#title').hide();
        $('#list').hide();
        $('#map').addClass('fullScreen');
        $('#hint').addClass('hint-full-screen');
        google.maps.event.trigger(map, "resize");
        $(window).resize();
    }
    function collapseScreen(){
        $('#table-control').show();
        $('#title').show();
        $('#list').show();
        $('#collapse').hide();
        $('#map').removeClass('fullScreen');
        $('#hint').removeClass('hint-full-screen');
        google.maps.event.trigger(map, "resize");
        $(window).resize();
    }
    function showProjectGlobalViews(element, id){
        var seconds = 0; // uptime in seconds
        var check = false;
        $('#sexy-maker-images-'+id).addClass('pch_loading');
        var timer = setInterval(function(){
                seconds++;
                if(seconds >= 1){
                    check = true;
                    clearInterval(timer);
                    jQuery.ajax({
                        url : "/project_global_views/ajax/"+id + "/map",
                        type: "GET",
                        cache: false,
                        success: function (html) {
                            var dump = $('<div />').append(html);
                            if( dump.children('.error').length == 1 ){
                                //do nothing
                            } else if ( dump.children('#attachment-type').val() ) {
                                jQuery('#dialogDetailValue').css({'top':'25%', 'left':'32%'});
                                jQuery('#dialogDetailValue').css({'padding-top':0, 'z-index':9999});
                                jQuery('#contentDialog').html(html);
                                showMe();
                            }
                            $('#sexy-maker-images-'+id).removeClass('pch_loading');
                        }
                    });
                }
            }, 800);
        element.onmouseout = function(){
            seconds = 0;
            if(!check) $('#sexy-maker-images-'+id).removeClass('pch_loading');
            clearInterval(timer);
        }
    }
</script>
