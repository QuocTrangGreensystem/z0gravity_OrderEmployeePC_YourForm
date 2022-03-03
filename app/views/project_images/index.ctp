<?php
function filesize_formatted($size) {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}
function get_mime_type($file) {
    // our list of mime types
    $mime_types = array(
            "pdf"=>"application/pdf"
            ,"exe"=>"application/octet-stream"
            ,"zip"=>"application/zip"
            ,"docx"=>"application/msword"
            ,"doc"=>"application/msword"
            ,"xls"=>"application/vnd.ms-excel"
            ,"ppt"=>"application/vnd.ms-powerpoint"
            ,"gif"=>"image/gif"
            ,"png"=>"image/png"
            ,"jpeg"=>"image/jpg"
            ,"jpg"=>"image/jpg"
            ,"mp3"=>"audio/mpeg"
            ,"wav"=>"audio/x-wav"
            ,"mpeg"=>"video/mpeg"
            ,"mpg"=>"video/mpeg"
            ,"mpe"=>"video/mpeg"
            ,"mov"=>"video/quicktime"
            ,"avi"=>"video/x-msvideo"
            ,"3gp"=>"video/3gpp"
            ,"css"=>"text/css"
            ,"jsc"=>"application/javascript"
            ,"js"=>"application/javascript"
            ,"php"=>"text/html"
            ,"htm"=>"text/html"
            ,"html"=>"text/html",
            'mp4' => 'video/mp4'
    );

    $extension = strtolower(end(explode('.',$file)));

    return @$mime_types[$extension];
}
    echo $html->script(array(
        'multipleUpload/plupload.full.min',
        'multipleUpload/jquery.plupload.queue-default',
        'jquery.flexslider-min',
        'jquery.fancybox.pack'
    ));
 ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('flexslider'); ?>
<?php echo $html->css(array('jquery.ui.custom', 'multipleUpload/jquery.plupload.queue', 'jquery.fancybox')); ?>
<style>
    #management {
        max-height: 500px;
        overflow: auto;
    }
    #slider .flex-active-slide {margin-right: 1px;}
    .display td, .display th{
        vertical-align: middle;
    }
    .wd-header th {
        text-align: left !important;
        padding-left: 10px;
    }
    .wd-button,
    .wd-button:hover,
    .wd-button:visited,
    .wd-button:active {
        color: #fff !important;
        border-radius: 4px;
        padding: 8px 10px;
        display: inline-block;
        text-decoration: none;
    }
    .wd-button {
        background-color: #5cb85c;
        border: 1px solid #4cae4c;
    }
    .wd-button-danger {
        background-color: #d9534f;
        border-color: #d43f3a;
    }
    .plupload_file_size, .plupload_file_name {
        color: #000 !important;
    }
    .wd-tab {
        margin: 0;
        margin: 0 auto;
/*        <?php if( $isTablet ): ?>
        width: 75%;
        <?php elseif( $isMobileOnly ): ?>width: auto;
        <?php else: ?>
        width: 60%;
        <?php endif ?>*/
    }
    .wd-tab.full {
        width: auto;
    }
    .wd-tab .wd-panel {
        border: 0;
    }
    .wd-tab .wd-panel:last-child {
        padding: 0;
    }
    .smart-phone {
    }
    .flexslider{
        border: none;
    }
    img{
        display: block;
        margin: auto;
        vertical-align: middle;
    }
    #wd-container-footer{
        display: none;
    }
    #open-modal.smart-phone{
        line-height: 36px;
    }
    .wd-section .flexslider{
        border: none;
    }
    .flexslider .flex-direction-nav li > a{
        background-color: #E2E7E9;
    }
	#carousel ul.slides img {
		width: 150px;
		height: 100px;
	}
</style>

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title" style="margin-left: 20%">
                    <h2 style="color: orange" class="wd-t1"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                    <a href="#" id="open-modal" class="btn-text smart-phone">
                        <i class="icon-plus icons"></i>
                        <span><?php echo __('Upload files') ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="text-download" value="<?php __('Download') ?>" />
                <input type="hidden" id="text-delete" value="<?php __('Delete') ?>" />
                <input type="hidden" id="www-url" value="<?php echo $html->url('/project_images/') ?>" />
                <input type="hidden" id="image-url" value="<?php echo $html->url($uploadUrl) ?>" />
                <input type="hidden" id="company-id" value="<?php echo $company_id ?>" />
                <input type="hidden" id="project-id" value="<?php echo $project_id ?>" />
                <?php
                if( !empty($images)):
                ?>

                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section">
                            <div id="slider" class="flexslider">
                                <ul class="slides">
                                    <?php
                                    foreach ($images as $image) {
                                        if( $image['ProjectImage']['type'] != 'image' )
                                            continue;
                                        $link = $this->Html->url(array('action' => 'attachment', $project_id, $image['ProjectImage']['id'], '?' => array('sid' => $api_key)), true);
                                        $url = $this->Html->url(array('action' => 'attachment', $project_id, $image['ProjectImage']['id'], '?' => array('sid' => $api_key)), true);
                                        if( file_exists($uploadFolder . $company_id . '/' . $project_id . '/l_' . $image['ProjectImage']['file']) )
                                            $url = $this->Html->url(array('action' => 'attachment', $project_id, $image['ProjectImage']['id'], 'l_','?' => array('sid' => $api_key)), true);
                                    ?>
                                        <li>
                                            <a href="<?php echo $link ?>" class="fancy" rel="gallery1"><img data-id="<?php echo $image['ProjectImage']['id'] ?>" src="<?php echo $url ?>" width="800" height="450" style="max-width: 800px; max-height: 450px;"alt=""></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div id="carousel" class="flexslider">
                                <ul class="slides">
                                    <?php
                                    foreach ($images as $image) {
                                        if( $image['ProjectImage']['type'] != 'image' )
                                            continue;
                                        $url = $this->Html->url(array('action' => 'attachment', $project_id, $image['ProjectImage']['id'],'r_', '?' => array('sid' => $api_key)), true);
                                    ?>
                                        <li>
                                            <img src="<?php echo $url ?>" alt="">
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                <div class="wd-tab smart-phone">
                    <div class="wd-panel">
                        <div class="wd-section">
                            <div id="management">
                                <table cellspacing="0" cellpadding="0" class="display">
                                    <thead>
                                        <tr class="wd-header">
                                            <th><?php __('Name') ?></th>
                                            <th width="15%"><?php __('Size') ?></th>
                                            <th><?php __('Action') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="list-image">
                                        <tr>
                                            <?php
                                            if( empty($images)):
                                                echo '<td colspan="3" id="images-empty" align="center">&nbsp;</td>';
                                            else :
                                                foreach( $images as $image ):
                                            ?>
                                            <td>
                                                <?php if( get_mime_type($image['ProjectImage']['file']) == 'application/pdf' ): ?>
                                                    <a href="https://docs.google.com/viewer?url=<?php echo urlencode($html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'])) ?>" target="_blank" class="show-window" title="<?php __('View on Google Docs') ?>"><?php echo $image['ProjectImage']['file'] ?></a>
                                                <?php else: ?>
                                                    <?php echo $image['ProjectImage']['file'] ?>
                                                <?php endif ?>
                                            </td>
                                            <td><?php echo filesize_formatted($image['ProjectImage']['size']) ?></td>
                                            <td class="wd-action" nowrap>
                                                <a title="<?php __('Download') ?>" href="<?php echo $html->url('/project_images/download/' . $image['ProjectImage']['id']) ?>" class="wd-download"><?php __('Download') ?></a>
                                                <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                                                    <div class="wd-bt-big"><a href="<?php echo $html->url('/project_images/delete/' . $image['ProjectImage']['id'] . '/' . $project_id) ?>" title="<?php __('Delete') ?>" class="wd-hover-advance-tooltip" onclick="return confirm('<?php __('Delete?') ?>')"><?php __('Delete') ?></a></div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div></div>
        </div>
    </div>
</div>

<!-- modal -->
<div id="upload-dialog" title="<?php __('Upload files') ?>">
    <div id="queue">
    </div>
</div>
<div id="show-window" title="<?php __('View file') ?>">
    <iframe id="x-frame" style="border: none;" frameborder="0" width="100%" height="100%"></iframe>
</div>

<script type="text/javascript">
<?php if( Configure::read('Config.langCode') == 'fr' ): ?>

// French (fr)
plupload.addI18n({"Stop Upload":"Arrêter l'envoi.","Upload URL might be wrong or doesn't exist.":"L'URL d'envoi est soit erronée soit n'existe pas.","tb":"To","Size":"Taille","Close":"Fermer","Init error.":"Erreur d'initialisation.","Add files to the upload queue and click the start button.":"Ajoutez des fichiers à la file d'attente de téléchargement et appuyez sur le bouton 'Démarrer l'envoi'","Filename":"Fichier","Image format either wrong or not supported.":"Le format d'image est soit erroné soit pas géré.","Status":"État","HTTP Error.":"Erreur HTTP.","Start Upload":"Charger","mb":"Mo","kb":"Ko","Duplicate file error.":"Erreur: Fichier déjà sélectionné.","File size error.":"Erreur de taille de fichier.","N/A":"Non applicable","gb":"Go","Error: Invalid file extension:":"Erreur: Extension de fichier non valide:","Select files":"Sélectionnez les fichiers","%s already present in the queue.":"%s déjà présent dans la file d'attente.","File: %s":"Fichier: %s","b":"o","Uploaded %d/%d files":"%d fichiers sur %d ont été envoyés","Upload element accepts only %d file(s) at a time. Extra files were stripped.":"Que %d fichier(s) peuvent être envoyé(s) à la fois. Les fichiers supplémentaires ont été ignorés.","%d files queued":"%d fichiers en attente","File: %s, size: %d, max file size: %d":"Fichier: %s, taille: %d, taille max. d'un fichier: %d","Drag files here.":"Déposez les fichiers ici.","Runtime ran out of available memory.":"Le traitement a manqué de mémoire disponible.","File count error.":"Erreur: Nombre de fichiers.","File extension error.":"Erreur d'extension de fichier","Error: File too large:":"Erreur: Fichier trop volumineux:","Add Files":"Ajouter"});
<?php endif ?>

function delete_image(id, e){
    var me = $(e);
    if( confirm('<?php __('Do you want to delete this picture?') ?>')){
        var data = {data:{id:id}};
        //TO DO: implement ajax call
        $.ajax({
            url: '<?php echo $html->url('/project_images/delete/') ?>',
            data: data,
            type: 'POST',
            success: function(data){
                if( data == 'ok'){
                    me.parent().parent().hide('slow').remove();
                }
            }
        });
    }
    return false;
}


(function($){

    $('#queue').pluploadQueue({
        // General settings
        runtimes : 'html5,html4,flash',
        url : "<?php echo $html->url('/project_images/upload/' . $company_id . '/' . $project_id) ?>",

        //chunk_size : '1mb',
        rename : true,
        dragdrop: true,
        multiple_queues: true,
        filters : {
            // Maximum file size
            //max_file_size : '100mb',
            // Specify what files to browse for
            mime_types: [
                {title : "PDF, Image and Video files", extensions : "<?php echo $allowedExtensions ?>"}
            ]
        },
        flash_swf_url : '<?php echo $html->url('/js/moxie.swf') ?>'
    });
    var uploader = $('#queue').pluploadQueue();
    var modal = $('#upload-dialog').dialog({
        autoOpen: false,
        width: 'auto',
        modal: true,
        //hide : { effect: "explode", duration: 300 },
        show : { effect: "drop", duration: 200 },
        open: function(e, ui){
            uploader.refresh();
        }
    });
    $('#open-modal').click(function(){
        modal.dialog('open');
    });
    var xwindow = $('#show-window').dialog({
        autoOpen : false,
        width : 900,
        height : 600,
        modal : true,
        hide : { effect: "explode", duration: 300 },
        show : { effect: "drop", duration: 200 }
    })
    $('.show-window').click(function(){
        $('#x-frame').prop('src', $(this).prop('href') + '&embedded=true');
        xwindow.dialog('option', 'title', $(this).text());
        xwindow.dialog('open');
        return false;
    });

    $("a.fancy").fancybox({
        type: 'image',
    });

    $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 150,
        itemMargin: 5,
        minItems: 5,
        asNavFor: '#slider',
        pauseOnHover: true,
      });

      $('#slider').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: true,
        slideshow: true,
        pauseOnAction: false,
        pauseOnHover: true,
        sync: "#carousel",
        prevText: '<i class="icon-arrow-left" aria-hidden="true"></i>',     
        nextText: '<i class="icon-arrow-right" aria-hidden="true"></i>',
      });

    // buildGallery();

})(jQuery);
var $listDimencision = <?php echo json_encode($listDimencision) ?>;
var img = $('#slider').find('img');
setTimeout(function(){
    $.each(img, function(ind, val){
        var _id = $(val).data('id');
        var _w = $listDimencision[_id] ? $listDimencision[_id]['width'] : 0;
		var _h = $listDimencision[_id] ? $listDimencision[_id]['height'] : 0;
        var t2 = _w/_h;
        if(_h > 450){
            _h = 450;
            _w = _h*t2;
        }
        if(_w > 800){
            _w = 800;
            _h = _w/t2;
        }
        if( _h) $(val).css('height', _h);
        if( _w) $(val).css('width', _w);
        var _top = (450 - _h)/2;
        $(val).css('margin-top', _top);
    });

}, 2000);
</script>
