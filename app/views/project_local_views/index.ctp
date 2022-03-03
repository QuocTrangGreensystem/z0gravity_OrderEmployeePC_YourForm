<?php
if($enable_newdesign) echo $html->css('preview/project_local');
$gapi = GMapAPISetting::getGAPI();
?>
<style type="text/css">
    .error-message {
        color: #FF0000;
        margin-left: 35px;
    }
    #download-place img{
        margin-right: 5px;
        vertical-align: middle;
    }
    #replace-attachment{
        margin-left: 20px;
    }
    #wd-container-main .wd-layout{
        padding-bottom: 10px;
    }
    .wd-input.wd-normal input,
    .wd-input.wd-normal label {
        float: none;
        width: auto;
    }
    .wd-input.wd-normal label {
        display: inline-block;
        margin-left: 5px;
        line-height: 20px;
    }
    #coord-input {
        padding: 7px;
        font-size: 12px;
        vertical-align: middle;
        border: 1px solid #bbb;
        background: #fff;
    }
    #display-maps {
        vertical-align: middle;
        margin: 0 10px;
        cursor: pointer;
    }
    fieldset div.wd-input {
        margin: 0;
    }
    #file-types {
        clear: both;
        line-height: 30px;
    }
    #file-types span {
        color: green;
    }
    .wd-tab .wd-panel{
        border: none;
    }
    #wd-container-footer{
        display: none;
    }
    .active-box{
        float: right;
        font-size: 20px;
        display: inline-block;
    }
</style>
<?php
if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)){
    $style = '';
} else {
    $style = 'display: none';
}

?>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab">
                <div class="wd-panel">
                    <?php echo $this->Session->flash(); ?>
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2" style="float: left;border: 0; margin-right: 20px; color: orange">
                            <span class="title-global"><?php echo $projectName['Project']['project_name'] ?></span>
                            <a href="#" style="<?php echo $style ?>" class="btn btn-globe" id="display-maps"></a>
                            <input style="<?php echo $style ?>" type="text" id="coord-input" size="40">
                            <img src="<?php echo $this->Html->url('/img/ajax-loader.gif') ?>" id="loader" style="vertical-align: middle; display: none;" />
                            
                        </h2>
                        <?php
                        echo $this->Form->create('ProjectLocalView', array(
                            'type' => 'file',
                            'url' => array('controller' => 'project_local_views', 'action' => 'upload',
                                $projectName['Project']['id'])));
                        ?>
                        <fieldset style="float: left">
                            <?php if ($projectLocalView) : ?>
                             <!-- If is_file = 1 then File else if is_file = 0 then Url -->
                                <div id="download-place">
                                    <?php
                                    if($projectLocalView['ProjectLocalView']['is_file']){
                                        echo $this->Html->link($this->Html->image('download.png') . __('Download', true), array(
                                                'action' => 'attachment', $projectName['Project']['id'], '?' => array('download' => true, 'sid' => $api_key)), array(
                                        'escape' => false,
                                        'id' => 'download-attachment'));
                                    }else{
                                        $is_http = $projectLocalView['ProjectLocalView']['is_https'] ? 'https://' : 'http://';
                                        $IFRAME = $is_http.$projectLocalView['ProjectLocalView']['attachment'] ;
                                        echo "<a href=".$is_http.$projectLocalView['ProjectLocalView']['attachment']." target='_blank'>".$this->Html->image('url.png') . __('URL ', true)."</a>";
                                    }
                                    
                                    if ((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) {
                                        echo $this->Html->link($this->Html->image('delete.png') . __('Delete', true), 'javascript:void(0);', array(
                                            'escape' => false,
                                            'id' => 'replace-attachment'));
                                        echo $html->image('ajax-loader.gif', array(
                                            'id' => 'loader',
                                            'style' => 'display: none; margin-left: 3px'
                                        ));
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <div id="upload-place" class="wd-submit" style="width: auto;padding: 0;margin: 0;<?php echo 'display:' . ($projectLocalView ? 'none' : 'block') ?>">
                                <?php if ((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                                     <div class="wd-input wd-normal" style="float: left;width: auto;">
                                         <?php
                                            $options=array(1=>__('File',true),0=>__('URL',true));
                                            $attributes=array('legend'=>false,'class'=>'r-right','div'=>true,'default'=>1);
                                            echo $this->Form->radio('is_file',$options,$attributes);
                                         ?>
                                    </div>
                                    <div class="wd-input" style="float: left;width: auto; clear: none; margin-right: 5px;">
                                        <?php
                                        echo $this->Form->input('attachment', array('div' => false, 'label' => false,
                                            'type' => 'file',
                                            'name' => 'FileField[attachment]',
                                            'style' => 'width: 200px'
                                        ));
                                        ?>
                                    </div>
                                    <div class="wd-input" style="float: left; width: auto; clear: none; margin-right: 5px; display: none;">
                                        <?php
                                        echo $this->Form->input('attachment', array('div' => false, 'label' => false,'id'=>'attachmentUrl',
                                            'type' => 'text',
                                            'style' => 'width: 200px',
                                            'placeholder' => 'Ex: www.example.com'
                                        ));
                                        ?>
                                    </div>
                                    <div style="float: left;margin-left: 5px;">
                                        <button type="submit" class="btn btn-save" id="btnSave">
                                            <span><?php __('Save') ?></span>
                                        </button>
                                    </div>
                                    <div id="file-types">
                                        <?php __('Allowed file types') ?>: <span><?php echo str_replace(',', ', ', $allowedFiles) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </fieldset>
                        <?php echo $this->Form->end(); ?>
                        <a href="#" class='active-box'><i class="icon-options"></i></a>
                        <br style="clear: both;" />
                    </div>
                    <div class="wd-section" id="wd-fragment-2">
                        <?php
                        $link = $this->Html->url(array('action' => 'attachment', $projectName['Project']['id'], '?' => array('sid' => $api_key)), true);
                        if ($projectLocalView && empty($noFileExists)) {
                            if (!preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectLocalView['ProjectLocalView']['attachment'])) {
                                $link = 'https://docs.google.com/gview?url=' . ($link) . '&embedded=true';
                            }
                        }
                        $is_link = 1;
                        if(!isset($IFRAME))
                        {
                            $IFRAME = $link;
                        }
                        else
                        {
                            $is_link = 1;
                            $IFRAME = $IFRAME;
                            $IFRAME_NAME = $IFRAME;
                            echo __('Local view: ', true);
                            echo $this->Html->link(
                                $IFRAME_NAME,$IFRAME,array('target' => '_blank')
                            );
                        }
                        ?>
                        <br />
                        <iframe src="<?php echo $IFRAME; ?>" style="width: 100%;height: 900px; border: 1px solid #D8D8D8;" id="local-frame"></iframe>
                        <iframe src="about:blank" style="width: 100%;height: 900px; border: 1px solid #D8D8D8; display: none" id="map-frame" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var state = 1;
    var coord = /^\s*(\-?[0-9]+\.[0-9]+)\s*,\s*(\-?[0-9]+\.[0-9]+)\s*$/;
    var gapi = <?php echo json_encode($gapi) ?>;
	var project_id = <?php echo json_encode($projectName['Project']['id']) ?>;
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
    $(document).ready(function(){
        $('#replace-attachment').click(function(){
            $('#loader').show();
            $.ajax({
                type : 'POST',
                url : '<?php echo $html->url('/project_local_views/delete/' . @$projectLocalView['ProjectLocalView']['id']) ?>',
                success : function(){
                    $('#loader').hide();
                    $('#download-place').remove();
                    $('#upload-place').show();
                    $('#local-frame').prop('src', 'about:blank');
                }
            });
        });
        var saving = false;
        $('#coord-input').val(<?php echo json_encode($projectName['Project']['address']) ?>).change(function(){
            if( saving )return false;
            var me = $(this);
            $('#loader').show();
            //get location
            //result may be null, empty string or an object
            getCode(function(result){
                if( result == null ){
                    me.css('color', 'red');
                    $('#loader').hide();
                    alert(<?php echo json_encode(__('Cannot find the location of this address', true)) ?>);
                } else {
                    $.ajax({
                        url: '<?php echo $this->Html->url('/project_local_views/saveAddress/' . $projectName['Project']['id']) ?>',
                        data: {
                            data:
                            {
                                address: me.val(),
                                latlng: result
                            }
                        },
                        type: 'POST',
                        complete: function(){
                            saving = false;
                            refreshMap(state == 0);
                            me.css('color', 'green');
                            refreshMap(true);
                            $('#loader').hide();
                        }
                    });
                }
            });
        });

        <?php if($projectName['Project']['address']): ?>
        refreshMap(true);
        <?php endif ?>

        $('#display-maps').click(function(){
            //show map, hide local view
			
			refreshMap(true);
            // if( state == 2 ){
                // $('#local-frame').hide();
                // $('#map-frame').show();
                // state = 0;
            // } else {
                // if( state == 1 ){
                    // refreshMap(true);
                // } else {
                    // $('#map-frame').hide();
                    // $('#local-frame').show();
                    // state = 2;
                // }
            // }
            // if( state == 2 ){
                // $('#display-maps').removeClass('grayscale');
            // } else {
                // $('#display-maps').addClass('grayscale');
            // }
        });
        $('.active-box').click(function(){
            $(this).closest('.wd-section').toggleClass('active');
            $(this).closest('.wd-section').find('#ProjectLocalViewIndexForm').toggleClass('active');
        });
    });
    $("#attachmentUrl").parent().hide();
    $('#ProjectLocalViewIsFile1').click(function(){
       if($('#ProjectLocalViewIsFile1').is(':checked')) {
            $("#ProjectLocalViewAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
        } else {
            $("#ProjectLocalViewAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
        }
    });
    $('#ProjectLocalViewIsFile0').click(function(){
       if($('#ProjectLocalViewIsFile1').is(':checked')) {
            $("#ProjectLocalViewAttachment").parent().show();
            $("#attachmentUrl").parent().hide();
        } else {
            $("#ProjectLocalViewAttachment").parent().hide();
            $("#attachmentUrl").parent().show();
        }
    });

    <?php
    if($is_link == -1)
    { ?>
    var iframe = document.getElementsByTagName('iframe')[0];
    var url = original_link;
    var getData = function (data) {
        if (data && data.query && data.query.results && data.query.results.resources && data.query.results.resources.content && data.query.results.resources.status == 200) loadHTML(data.query.results.resources.content);
        else if (data && data.error && data.error.description) loadHTML(data.error.description);
        else loadHTML('Error: Cannot load ' + url);
    };
    var loadURL = function (src) {
        url = src;
        var script = document.createElement('script');
        script.src = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20data.headers%20where%20url%3D%22' + encodeURIComponent(url) + '%22&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=getData';
        document.body.appendChild(script);
    };
    var loadHTML = function (html) {
        iframe.src = 'about:blank';
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(html.replace(/<head>/i, '<head><base href="' + url + '"><scr' + 'ipt>document.addEventListener("click", function(e) { if(e.target && e.target.nodeName == "A") { e.preventDefault(); parent.loadURL(e.target.href); } });</scr' + 'ipt>'));
        iframe.contentWindow.document.close();
    }

    loadURL(original_link);
    <?php } ?>
function getCode(callback){
    var address = $.trim($('#coord-input').val());
    if( !address )return callback('');
    if( matches = address.match(coord) ){
        return callback({lat:matches[1], lng: matches[2]});
    }
    var url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURIComponent(address) + '&key=' + gapi;
    $.ajax({
        url: url,
        type: 'GET',
        success: function(result){
            if( result.status == 'OK' ){
                callback(result.results[0].geometry.location);
            } else {
                callback(null);
            }
        }
    });
}
</script>

<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gapi ?>&amp;callback=initMap"></script>-->
