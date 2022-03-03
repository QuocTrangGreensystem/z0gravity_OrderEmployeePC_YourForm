<?php

function filesize_formatted($size)
{
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

echo $html->script(array(
    'multipleUpload/plupload.full.min',
    'multipleUpload/jquery.plupload.queue-default',
    'jplayer/jquery.jplayer.min',
    'jplayer/jplayer.playlist'
));
echo $html->css(array(
    'multipleUpload/jquery.plupload.queue',
    'blue.monday/css/jplayer.blue.monday'
));

$media = array();
$web = array();

foreach ($videos as $video) {
    $v = $video['ProjectImage'];
    $data = array(
        'title' => $v['file'],
        'url' => $v['file'],
        'size' => filesize_formatted($v['size'])
    );
    $data['file'] = (bool) $v['is_file'];
    $data['id'] = $v['id'];
    $data['project_id'] = $v['project_id'];
    if( $data['file'] ){
        $url = $this->Html->url('/video/stream/' . $project_id . '/' . $v['id'] . '/' . $data['url']);
        //$url = $this->Html->url('/files/projects/' . $company_id . '/' . $project_id . '/' . $v['file']);
        $type = strtolower(end(explode('.', $v['file'])));
        switch($type){
            case 'ogg':
            case 'ogv':
                $data['ogv'] = $url;
            break;
            case 'mp4':
            case 'm4v':
                $data['m4v'] = $url;
            break;
            default:
                $data[$type] = $url;
            break;
        }
        $media[] = $data;
    } else {
        $web[] = $data;
    }
}

?>
<style>
    .plupload_file_size, .plupload_file_name {
        color: #000 !important;
    }
    #upload-dialog,
    #url-dialog {
        display: none;
    }
    #url-dialog {
        font-size: 12px;
        color: #333;
    }
    #url-dialog label {
        width: auto;
        float: none;
        padding: 0 20px;
    }
    #url-dialog input {
        float: none;
        width: 300px;
    }
    #jplayer-container{
        margin: 0 auto;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
    .wd-title{
        margin-left: 28.5%;
    }
	<?php 
    if( !$pmCanChange || (!empty($canModified) && !$_isProfile && !$canModified) || ($_isProfile && !$_canWrite)) :?>
	.z0-action .z0-delete{
		display: none;
	}
	<?php endif; ?>
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 style="color: orange" class="wd-t1"><?php echo sprintf(__("%s", true), $projectName['Project']['project_name']); ?></h2>
                    <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                    <a href="#" id="open-modal" class="btn-text smart-phone">
                        <i class="icon-plus icons"></i>
                        <span><?php echo __('Upload files') ?></span>
                    </a>
                    <a href="#" id="open-modal-url" class="btn-text smart-phone">
                        <i class="icon-link icons"></i>
                        <span><?php echo __('URL') ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php echo $this->Session->flash() ?>
                <input type="hidden" id="text-download" value="<?php __('Download') ?>" />
                <input type="hidden" id="text-delete" value="<?php __('Delete') ?>" />
                <input type="hidden" id="www-url" value="<?php echo $html->url('/project_images/') ?>" />
                <input type="hidden" id="image-url" value="<?php echo $html->url($uploadUrl) ?>" />
                <input type="hidden" id="company-id" value="<?php echo $company_id ?>" />
                <input type="hidden" id="project-id" value="<?php echo $project_id ?>" />
                <div id="jplayer-container" class="jp-video" role="application" aria-label="media player" style="overflow: auto">
                    <div class="jp-type-playlist">
                        <!-- player and list section -->
                        <div id="jplayer" class="jp-jplayer"></div>
                        <!-- controls -->
                        <div class="jp-gui">
                            <div class="jp-video-play">
                                <button class="jp-video-play-icon" role="button" tabindex="0">play</button>
                            </div>
                            <div class="jp-interface">
                                <div class="jp-progress">
                                    <div class="jp-seek-bar">
                                        <div class="jp-play-bar"></div>
                                    </div>
                                </div>
                                <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
                                <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                                <div class="jp-controls-holder">
                                    <div class="jp-controls">
                                        <button class="jp-previous" role="button" tabindex="0">previous</button>
                                        <button class="jp-play" role="button" tabindex="0">play</button>
                                        <button class="jp-next" role="button" tabindex="0">next</button>
                                        <button class="jp-stop" role="button" tabindex="0">stop</button>
                                    </div>
                                    <div class="jp-volume-controls">
                                        <button class="jp-mute" role="button" tabindex="0">mute</button>
                                        <div class="jp-volume-bar">
                                            <div class="jp-volume-bar-value"></div>
                                        </div>
                                        <button class="jp-volume-max" role="button" tabindex="0">max volume</button>
                                    </div>
                                    <div class="jp-switchers">
                                        <span class="jp-item jp-text"><?php __('Autoplay') ?></span>
                                        <span class="onoffswitch jp-item">
                                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="jp-autoplay-checkbox">
                                            <label class="onoffswitch-label" for="jp-autoplay-checkbox">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="jp-toggles">
                                        <button class="jp-repeat" role="button" tabindex="0">repeat</button>
                                        <button class="jp-shuffle" role="button" tabindex="0">shuffle</button>
                                        <button class="jp-full-screen" role="button" tabindex="0">full screen</button>
                                    </div>
                                </div>
                                <div class="jp-details">
                                    <div class="jp-title" aria-label="title">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <!-- /controls -->
                        <div class="playlist-holder">
                            <!-- playlist -->
                            <div class="jp-playlist" id="jp-main-playlist">
                                <ul>
                                    <li>&nbsp;</li>
                                </ul>
                            </div>
                            <!-- /playlist -->
<?php if( !empty($web) ): ?>
                            <!-- url list -->
                            <div class="jp-playlist z0-url-list">
                                <ul>
<?php foreach ($web as $m) : ?>
                                    <li>
                                        <div>
                                            <a href="<?php echo $m['url'] ?>" target="_blank" class='z0-url'><?php echo $m['title'] ?></a>
                                            <span class="z0-action">
                                                <a href="javascript:;" class="z0-delete"><?php echo $m['project_id'] ?>/<?php echo $m['id'] ?></a>
                                            </span>
                                        </div>
                                    </li>
<?php endforeach ?>
                                </ul>
                            </div>
                        <!-- /url list -->
<?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
            </div></div>
        </div>
    </div>
</div>
<!-- modals-->
<div id="upload-dialog" title="<?php __('Upload files') ?>">
    <div id="queue">
    </div>
</div>

<div id="url-dialog" class="buttons">
    <fieldset>
        <?php echo $this->Form->create(false, array('action' => '/saveUrl/' . $project_id, 'id' => 'url-form')) ?>
        <div class="wd-input">
            <?php echo $this->Form->input('url', array('label' => __('URL', true), 'div' => false, 'id' => 'input-url')) ?>
        </div>
        <ul class="type_buttons">
            <li><a href="javascript:;" onclick="$('#url-dialog').dialog('close')" class="cancel"><?php __("No") ?></a></li>
            <li><button type="submit" class="btn btn-ok"><span><?php __('Yes') ?></span></button></li>
        </ul>
    </fieldset>
</div>

<script type="text/javascript">
var wdTable = $('#jplayer-container');
var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 500) ? 500 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 500) ? 500 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});

<?php if( $langCode == 'fr' ): ?>

// French (fr)
plupload.addI18n({"Stop Upload":"Arrêter l'envoi.","Upload URL might be wrong or doesn't exist.":"L'URL d'envoi est soit erronée soit n'existe pas.","tb":"To","Size":"Taille","Close":"Fermer","Init error.":"Erreur d'initialisation.","Add files to the upload queue and click the start button.":"Ajoutez des fichiers à la file d'attente de téléchargement et appuyez sur le bouton 'Démarrer l'envoi'","Filename":"Fichier","Image format either wrong or not supported.":"Le format d'image est soit erroné soit pas géré.","Status":"État","HTTP Error.":"Erreur HTTP.","Start Upload":"Charger","mb":"Mo","kb":"Ko","Duplicate file error.":"Erreur: Fichier déjà sélectionné.","File size error.":"Erreur de taille de fichier.","N/A":"Non applicable","gb":"Go","Error: Invalid file extension:":"Erreur: Extension de fichier non valide:","Select files":"Sélectionnez les fichiers","%s already present in the queue.":"%s déjà présent dans la file d'attente.","File: %s":"Fichier: %s","b":"o","Uploaded %d/%d files":"%d fichiers sur %d ont été envoyés","Upload element accepts only %d file(s) at a time. Extra files were stripped.":"Que %d fichier(s) peuvent être envoyé(s) à la fois. Les fichiers supplémentaires ont été ignorés.","%d files queued":"%d fichiers en attente","File: %s, size: %d, max file size: %d":"Fichier: %s, taille: %d, taille max. d'un fichier: %d","Drag files here.":"Déposez les fichiers ici.","Runtime ran out of available memory.":"Le traitement a manqué de mémoire disponible.","File count error.":"Erreur: Nombre de fichiers.","File extension error.":"Erreur d'extension de fichier","Error: File too large:":"Erreur: Fichier trop volumineux:","Add Files":"Ajouter"});
<?php endif ?>

var _translate = <?php echo json_encode(array(
    'delete?' => __('Delete?', true)
)) ?>;

$(document).ready(function(){

    $('#queue').pluploadQueue({
        // General settings
        runtimes : 'html5,html4,flash',
        url : "<?php echo $html->url('/video/upload/' . $project_id) ?>",

        //chunk_size : '1mb',
        rename : true,
        dragdrop: true,
        multiple_queues: true,
        filters : {
            // Maximum file size
            //max_file_size : '100mb',
            // Specify what files to browse for
            mime_types: [
                {title : 'mp4,m4v,ogg,webm', extensions : '<?php echo $allowedExtensions ?>'}
            ]
        },
        flash_swf_url : '<?php echo $html->url('/js/moxie.swf') ?>'
    });
    var uploader = $('#queue').pluploadQueue();
    var modal = $('#upload-dialog').dialog({
        autoOpen: false,
        width: 'auto',
        modal: true,
        open: function(e, ui){
            uploader.refresh();
        }
    });
    var modal2 = $('#url-dialog').dialog({
        autoOpen: false,
        width: 385,
        modal: true,
        open: function(e, ui){
            $('#input-url').val('');
        }
    });
    $('#open-modal').click(function(){
        modal.dialog('open');
    });
    $('#open-modal-url').click(function(){
        modal2.dialog('open');
    });
    $('#url-form').submit(function(){
        var url = $.trim($('#input-url').val());
        if( !url ){
            return false;
        }
    });
    /*
    jPlayer setup
     */
    var size = {
        width: 800,
        height: 450,
        cssClass: 'jp-video-800'
    };
    //build media
    var media = <?php echo json_encode($media) ?>;
    //get setting from cookies
    var jSettings = {
        volume: parseFloat($.z0Cookie.get('volume', 0.8)),
        autoplay: parseInt($.z0Cookie.get('autoplay', 0)),
        mute: parseInt($.z0Cookie.get('mute', 0))
    };
    //setting up player
    var jl = new jPlayerPlaylist({
        jPlayer: "#jplayer",
        cssSelectorAncestor: "#jplayer-container",
        playlist: '#jp-main-playlist'
    }, media, {
        swfPath: '<?php echo $html->url('/js/jplayer') ?>',
        supplied: 'm4v, webmv, ogv, flv',
        solution: 'html, flash',
        useStateClassSkin: true,
        autoBlur: false,
        smoothPlayBar: true,
        keyEnabled: true,
        size: size,
        autoNext: false,
        ended: function(){
            $('#jplayer video, #jplayer #jp_flash_0').hide();
        },
        play: function(e){
            $('#jplayer video, #jplayer #jp_flash_0').show();

            //location.href = e.jPlayer.status.src;
        },
        ready: function(e){
            //apply settings
            $('#jplayer').jPlayer('volume', jSettings.volume);
            if( jSettings.mute )$('#jplayer').jPlayer('mute');
            jl.options.autoNext = jSettings.autoplay;
            //apply UI changes
            $('#jp-autoplay-checkbox').prop('checked', jSettings.autoplay);
        },
        volumechange: function(e){
            var muted = e.jPlayer.options.muted;
            if( muted ){
                $.z0Cookie.set('mute', 1);
            } else {
                $.z0Cookie.set('mute', 0);
            }
            var volume = Math.round10(e.jPlayer.options.volume, -2);
            $.z0Cookie.set('volume', volume);
        }
    });

    //auto play
    $('#jp-autoplay-checkbox').click(function(){
        var amount = $(this).prop('checked');
        $.z0Cookie.set('autoplay', amount ? 1 : 0);
        jl.options.autoNext = amount;
    });

    //innate url
    $('.z0-url-list .z0-delete').click(function(){
        if( confirm(_translate['delete?']) ){
            location.href = Azuree.root + 'video/delete/' + $(this).text();
        }
        return false;
    });
});
</script>
