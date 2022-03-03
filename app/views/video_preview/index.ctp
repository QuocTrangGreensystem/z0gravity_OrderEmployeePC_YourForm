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
    // 'jplayer/jplayer.playlist_preview',
    'dropzone.min',
));
echo $html->css(array(
    'multipleUpload/jquery.plupload.queue',
    'blue.monday/css/jplayer.blue.monday',
	'preview/project_video',
    'dropzone.min',
));

$media = array();
$web = array();
// debug( $videos); exit;
foreach ($videos as $video) {
    $v = $video['ProjectImage'];
	$thumbnail_src = $this->Html->url('/img/new-icon/video_thumb.png');
	if( !empty($v['thumbnail'])) $thumbnail_src  = $this->Html->url(array('controller' => 'project_images', 'action' => 'get_thumbnail', $project_id, $v['id'], '?' => array('sid' => $api_key)), true);
	// debug($thumbnail_src );
    $data = array(
        'title' => $v['file'],
        'url' => $v['file'],
        'size' => filesize_formatted($v['size']),
        'thumbnail_src' => $thumbnail_src ? $thumbnail_src : '',
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

// debug( $media); exit;
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
	#layout{
		background: #f2f5f7;
	}
	.wd-layout > .wd-main-content > .wd-tab > .wd-panel{
		max-width: 1200px;
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
        <div class="wd-main-content new-design">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
				<div class="wd-list-project">
					<div class="wd-title wd-hide">
						<a href="javascript:void(0);" class="btn btn-expand" id="table-expand" onclick="expandTable();" title="<?php __("Expand"); ?>"></a>
						<a href="javascript:void(0);" class="btn btn-table-collapse" id="table-collapse" onclick="collapse_table();" title="<?php __('Collapse table') ?>" style="display: none;"></a>
						<!-- <a class="add-new-item" href="javascript:;"  onclick="addNewDeliverablesButton();" ><img title="Add an item" src="/img/new-icon/add.png"></a> -->

					</div>
					<div id="message-place">
						<?php echo $this->Session->flash(); ?>
					</div>
					<!-- <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div> -->
					<br clear="all"  />
					<div class="wd-table-container">
						<?php if(($canModified && !$_isProfile) || $_canWrite){ ?>
							<div class="wd-popup-container">
								<div class="wd-popup">
									<?php
									echo $this->Form->create('popupUpload', array(
										'type' => 'POST',
										'url' => array('controller' => $this->params['controller'], 'action' => 'saveUrl', $projectName['Project']['id'])));
									echo $this->Form->input('url', array(
										'class' => 'not_save_history',
										'label' => array(
											'class' => 'label-has-sub',
											'text' =>__('URL',true),
											'data-text' => __('(optionnel)', true),
											),
										'name' => 'data[url]',
										'type' => 'text',
										'id' => 'newVideoURL', 
										'placeholder' => __('https://', true) ,  
										));
									echo $this->Form->input('isThumb', array(
										'class' => 'not_save_history',
										'type' => 'hidden',
										'id' => 'isThumb', 
										'value' => '',
										));
									echo $this->Form->input('videoName', array(
										'class' => 'not_save_history',
										'type' => 'hidden',
										'id' => 'videoName', 
										'value' => '',
										));
									echo $this->Form->input('videoId', array(
										'class' => 'not_save_history',
										'type' => 'hidden',
										'id' => 'videoId', 
										'value' => '',
										));
									echo $this->Form->input('thumbExt', array(
										'class' => 'not_save_history',
										'type' => 'hidden',
										'id' => 'thumbExt', 
										'value' => '',
										));
										?>

										<div id="popup_template_attach" >
											<div class="heading">
												<?php echo __('or', true); ?>
											</div> 
											<div class="trigger-upload"><div id="upload-popup" method="post" action="<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'add_new_video', $projectName['Project']['id'] ));?>" class="dropzone" value="" >
											</div></div>
										</div>
										<?php
									echo $this->Form->end(__('Add new video', true));
									?>
								</div>
								<a class="add-new-item" href="javascript:;"><img title="Add an item" src="/img/new-icon/add.png"></a>
							</div>
						<?php } ?>
					</div>
					<div id="jplayer-container" class="jp-video" role="application" aria-label="media player" style="overflow: auto">
						<div class="jp-type-playlist">
							<!-- player and list section -->
							<div class="jp-jplayer-container">
								<button class="jp-full-screen" role="button" tabindex="0"></button>
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
											<div class="jp-controls clearfix">
												<button class="jp-previous" role="button" tabindex="0"><i class="icon-control-rewind"></i></button>
												<button class="jp-play" role="button" tabindex="0"><i class="icon-control-play"></i></button>
												<button class="jp-stop" role="button" tabindex="0"><i class="icon-control-stop"></i></button>
												<button class="jp-next" role="button" tabindex="0"><i class="icon-control-forward"></i></button>
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
								<?php if( !empty($web) ){ ?>
									<!-- url list -->
									<div class="jp-playlist z0-url-list">
										<ul>
											<?php foreach ($web as $m) { ?>
												<li class="video-item">
													<div>
														<a href="<?php echo $m['url'] ?>" target="_blank" class='z0-url'><span><?php echo $m['title'] ?></span><img class='video_thumbail' src='<?php echo  $m['thumbnail_src'] ? $m['thumbnail_src'] : ''; ?>' ></a>
														<span class="z0-action">
															<a href="javascript:;" class="z0-delete"><?php echo $m['project_id'] ?>/<?php echo $m['id'] ?></a>
														</span>
													</div>
												</li>
											<?php } ?>
										</ul>
									</div>
								<!-- /url list -->
								<?php } ?>
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
/* 
// 1111
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
*/
<?php if( $langCode == 'fr' ): ?>

// French (fr)
plupload.addI18n({"Stop Upload":"Arrêter l'envoi.","Upload URL might be wrong or doesn't exist.":"L'URL d'envoi est soit erronée soit n'existe pas.","tb":"To","Size":"Taille","Close":"Fermer","Init error.":"Erreur d'initialisation.","Add files to the upload queue and click the start button.":"Ajoutez des fichiers à la file d'attente de téléchargement et appuyez sur le bouton 'Démarrer l'envoi'","Filename":"Fichier","Image format either wrong or not supported.":"Le format d'image est soit erroné soit pas géré.","Status":"État","HTTP Error.":"Erreur HTTP.","Start Upload":"Charger","mb":"Mo","kb":"Ko","Duplicate file error.":"Erreur: Fichier déjà sélectionné.","File size error.":"Erreur de taille de fichier.","N/A":"Non applicable","gb":"Go","Error: Invalid file extension:":"Erreur: Extension de fichier non valide:","Select files":"Sélectionnez les fichiers","%s already present in the queue.":"%s déjà présent dans la file d'attente.","File: %s":"Fichier: %s","b":"o","Uploaded %d/%d files":"%d fichiers sur %d ont été envoyés","Upload element accepts only %d file(s) at a time. Extra files were stripped.":"Que %d fichier(s) peuvent être envoyé(s) à la fois. Les fichiers supplémentaires ont été ignorés.","%d files queued":"%d fichiers en attente","File: %s, size: %d, max file size: %d":"Fichier: %s, taille: %d, taille max. d'un fichier: %d","Drag files here.":"Déposez les fichiers ici.","Runtime ran out of available memory.":"Le traitement a manqué de mémoire disponible.","File count error.":"Erreur: Nombre de fichiers.","File extension error.":"Erreur d'extension de fichier","Error: File too large:":"Erreur: Fichier trop volumineux:","Add Files":"Ajouter"});
<?php endif ?>

var _translate = <?php echo json_encode(array(
    'delete?' => __('Delete?', true)
)) ?>;

$(document).ready(function(){

    
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
            location.href = Azuree.root + '<?php echo $this->params['controller'];?>/delete/' + $(this).text();
        }
        return false;
    });
	function collapse_table() {
		$('#table-collapse').hide();
		$('#expand').show();
		$('.wd-panel').removeClass('treeExpand');
		isFull = false;
		$(window).trigger('resize');
	}
	function expandTable() {
		$('.wd-panel').addClass('treeExpand');
		$('#table-collapse').show();
		$('#expand').hide();
		isFull = true;
		$(window).trigger('resize');
	}
	
	$('.add-new-item').on('click', function(){
		$("#popupUploadIndexForm").trigger('reset');
		$('.wd-popup-container').toggleClass('open');
	});
});



/* Dropzone with form  
		 * By Dai Huynh  22-10-2018
		 */ 
		function dataURItoBlob(dataURI) {
			'use strict'
			var byteString, 
				mimestring 

			if(dataURI.split(',')[0].indexOf('base64') !== -1 ) {
				byteString = atob(dataURI.split(',')[1])
			} else {
				byteString = decodeURI(dataURI.split(',')[1])
			}

			mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0]

			var content = new Array();
			for (var i = 0; i < byteString.length; i++) {
				content[i] = byteString.charCodeAt(i)
			}

			return new Blob([new Uint8Array(content)], {type: mimestring});
		}
		// $(function() {
			var currentFile, currentFile_thumb;
			var time = 15; // Set specific time in second for thumbnail
			var scale = 1; // Scale image

			// Set dimensions of thumbnail
			var thumbnailDimensions = {
			  width: 740,
			  height: 460
			};
			var reader = '';
			var blob = '';
			var is_thumb = 0;
			var video_name = '';
			var video_id = '';
			if( $('#upload-popup').length){
				var popupDropzone = new Dropzone("#upload-popup",{
					maxFiles: 1,
					autoProcessQueue: false,
					addRemoveLinks: true,
					maxFilesize: 512,
				});
				popupDropzone.on("success", function(file, respon) {
					is_thumb = 0;
					popupDropzone.removeAllFiles();
					var data = $.parseJSON(respon);
					if ( data.is_upload_file ) {
						if( blob ) {
							popupDropzone.addFile(blob);
							$('#isThumb').val(1);
							$('#videoName').val(data.ProjectImage.file);
							$('#videoId').val(data.ProjectImage.id);
							$('#thumbExt').val('png');
							console.log(video_id , video_name);
							
							popupDropzone.processQueue();
							
						}
					}
					location.reload();
				});
				popupDropzone.on("queuecomplete", function(file) {
					// location.reload();
					if( $('#isThumb').val() ) location.reload();
				});
				// popupDropzone.on("removedfile", function(file) {
					// popupDropzone.removeAllFiles(true);
				// });
				$('#popupUploadIndexForm').on('submit', function(e){
					$('#popupUploadName').val($('#newDocName').val());
					$('#popupUploadUrl').val($('#newDocURL').val());
					

					if(popupDropzone.files.length){
						e.preventDefault();
						$('#popupUploadIndexForm').parent('.wd-popup').addClass('loading');
						popupDropzone.processQueue();
					}else{
						if( $.trim($('#newVideoURL').val()) =='' ){
							$('#newVideoURL').css('border-color', 'red');
							e.preventDefault();
						}
					}
				});
				popupDropzone.on('sending', function(file, xhr, formData) {
					// Append all form inputs to the formData Dropzone will POST
					var data = $('#popupUploadIndexForm').serializeArray();
					$.each(data, function(key, el) {
						formData.append(el.name, el.value);
					});
					// if($('#isThumb').val()){
						// $('#isThumb').val('');
						// $('#videoName').val('');
						// $('#videoId').val('');
					// }
					
				});
				popupDropzone.on('addedfile', function(file) {
					$('#newVideoURL').css('border-color','');
					console.log(currentFile ,file);
					if (currentFile){
						if (currentFile.name == file.name && currentFile.size == file.size) {
							popupDropzone.removeFile(currentFile);
							if(currentFile_thumb) popupDropzone.removeFile(currentFile_thumb);
							currentFile = file;
							currentFile_thumb = '';
						}
					}
					if( file.name.lastIndexOf('_thumb') == (file.name.length - 6)){
						if (currentFile_thumb) popupDropzone.removeFile(currentFile_thumb);
						currentFile_thumb = file;
					} 
					
					// var input_thumb = $('#newVideoThumb');				
					if( file.status != 'error' && file.type.match('video')){
						$('#popupUploadIndexForm').parent('.wd-popup').addClass('loading_thumb');
						reader = new FileReader();
						/* Create thumb */		
						if( reader){
							reader.onload = (function (file){
								if( file.size / 1024 / 1024 <= popupDropzone.options.maxFilesize) {
									return function (evt){

										
										var video = document.createElement('video');
										video.setAttribute('src', evt.target.result);							
										video.addEventListener('loadedmetadata', function() {
											this.currentTime = time; //Set current time of video after metadat loaded
										}, false);

										// Create thumbnail after video data loaded
										video.addEventListener('loadeddata', function() {
										var canvas = document.createElement("canvas");
										canvas.width = thumbnailDimensions.width * scale;
										canvas.height = thumbnailDimensions.height * scale;
										canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

										var img = document.getElementById("newVideoThumb");
										// img.src = canvas.toDataURL();		                
										// img.value = canvas.toDataURL();
										// console.log( canvas.toDataURL()) ;
										var img  = document.querySelector('.dz-image img');
										var _src = canvas.toDataURL('image/png');
										img.src = _src;

										video.setAttribute('currentTime', 0); // Reset video current time
										blob = dataURItoBlob(_src);
										blob.name = file.name+'_thumb';
										// console.log( blob);
										//popupDropzone.addFile(blob);
										
										}, false);
										$('#popupUploadIndexForm').parent('.wd-popup').removeClass('loading_thumb');
									}	
								}		
							})(file);
							reader.readAsDataURL(file);
						}
						/* End Create thumb */
					}
				});
			}
		// });
			$('#newVideoURL').on('focus', function(){
				$(this).css('border-color','');
			});
	/* End Dropzone with form  */ 
	
	/*
 * Playlist Object for the jPlayer Plugin
 * http://www.jplayer.org
 *
 * Copyright (c) 2009 - 2014 Happyworm Ltd
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/MIT
 *
 * Author: Mark J Panaghiston
 * Version: 2.4.1
 * Date: 19th November 2014
 *
 * Requires:
 *  - jQuery 1.7.0+
 *  - jPlayer 2.8.2+
 */

/*global jPlayerPlaylist:true */

(function($, undefined) {

	jPlayerPlaylist = function(cssSelector, playlist, options) {
		var self = this;

		this.current = 0;
		this.loop = false; // Flag used with the jPlayer repeat event
		this.shuffled = false;
		this.removing = false; // Flag is true during remove animation, disabling the remove() method until complete.

		this.cssSelector = $.extend({}, this._cssSelector, cssSelector); // Object: Containing the css selectors for jPlayer and its cssSelectorAncestor
		this.options = $.extend(true, {
			keyBindings: {
				next: {
					key: 221, // ]
					fn: function() {
						self.next();
					}
				},
				previous: {
					key: 219, // [
					fn: function() {
						self.previous();
					}
				},
				shuffle: {
					key: 83, // s
					fn: function() {
						self.shuffle();
					}
				}
			},
			stateClass: {
				shuffled: "jp-state-shuffled"
			},
			autoNext: true,
			web: []
		}, this._options, options); // Object: The jPlayer constructor options for this playlist and the playlist options

		this.playlist = []; // Array of Objects: The current playlist displayed (Un-shuffled or Shuffled)
		this.original = []; // Array of Objects: The original playlist

		this._initPlaylist(playlist); // Copies playlist to this.original. Then mirrors this.original to this.playlist. Creating two arrays, where the element pointers match. (Enables pointer comparison.)

		// Setup the css selectors for the extra interface items used by the playlist.
		this.cssSelector.details = this.cssSelector.cssSelectorAncestor + " .jp-details"; // Note that jPlayer controls the text in the title element.
		if( typeof this.cssSelector.playlist == 'undefined' ){
			this.cssSelector.playlist = this.cssSelector.cssSelectorAncestor + " .jp-playlist";
		}
		this.cssSelector.next = this.cssSelector.cssSelectorAncestor + " .jp-next";
		this.cssSelector.previous = this.cssSelector.cssSelectorAncestor + " .jp-previous";
		this.cssSelector.shuffle = this.cssSelector.cssSelectorAncestor + " .jp-shuffle";
		this.cssSelector.shuffleOff = this.cssSelector.cssSelectorAncestor + " .jp-shuffle-off";

		// Override the cssSelectorAncestor given in options
		this.options.cssSelectorAncestor = this.cssSelector.cssSelectorAncestor;

		// Override the default repeat event handler
		this.options.repeat = function(event) {
			self.loop = event.jPlayer.options.loop;
		};

		// Create a ready event handler to initialize the playlist
		$(this.cssSelector.jPlayer).bind($.jPlayer.event.ready, function() {
			self._init();
		});

		// Create an ended event handler to move to the next item
		$(this.cssSelector.jPlayer).bind($.jPlayer.event.ended, function() {
			if( self.options.autoNext ){
				self.next();
			}
		});

		// Create a play event handler to pause other instances
		$(this.cssSelector.jPlayer).bind($.jPlayer.event.play, function() {
			$(this).jPlayer("pauseOthers");
		});

		// Create a resize event handler to show the title in full screen mode.
		$(this.cssSelector.jPlayer).bind($.jPlayer.event.resize, function(event) {
			if(event.jPlayer.options.fullScreen) {
				$(self.cssSelector.details).show();
			} else {
				$(self.cssSelector.details).hide();
			}
		});

		// Create click handlers for the extra buttons that do playlist functions.
		$(this.cssSelector.previous).click(function(e) {
			e.preventDefault();
			self.previous();
			self.blur(this);
		});

		$(this.cssSelector.next).click(function(e) {
			e.preventDefault();
			self.next();
			self.blur(this);
		});

		$(this.cssSelector.shuffle).click(function(e) {
			e.preventDefault();
			if(self.shuffled && $(self.cssSelector.jPlayer).jPlayer("option", "useStateClassSkin")) {
				self.shuffle(false);
			} else {
				self.shuffle(true);
			}
			self.blur(this);
		});
		$(this.cssSelector.shuffleOff).click(function(e) {
			e.preventDefault();
			self.shuffle(false);
			self.blur(this);
		}).hide();

		// Put the title in its initial display state
		if(!this.options.fullScreen) {
			$(this.cssSelector.details).hide();
		}

		// Remove the empty <li> from the page HTML. Allows page to be valid HTML, while not interfereing with display animations
		$(this.cssSelector.playlist + " ul").empty();

		// Create .on() handlers for the playlist items along with the free media and remove controls.
		this._createItemHandlers();

		// Instance jPlayer
		$(this.cssSelector.jPlayer).jPlayer(this.options);
	};

	jPlayerPlaylist.prototype = {
		_cssSelector: { // static object, instanced in constructor
			jPlayer: "#jquery_jplayer_1",
			cssSelectorAncestor: "#jp_container_1"
		},
		_options: { // static object, instanced in constructor
			playlistOptions: {
				autoPlay: false,
				loopOnPrevious: false,
				shuffleOnLoop: true,
				enableRemoveControls: false,
				displayTime: 'slow',
				addTime: 'fast',
				removeTime: 'fast',
				shuffleTime: 'slow',
				itemClass: "jp-playlist-item",
				freeGroupClass: "jp-free-media",
				freeItemClass: "jp-playlist-item-free",
				removeItemClass: "jp-playlist-item-remove"
			}
		},
		option: function(option, value) { // For changing playlist options only
			if(value === undefined) {
				return this.options.playlistOptions[option];
			}

			this.options.playlistOptions[option] = value;

			switch(option) {
				case "enableRemoveControls":
					this._updateControls();
					break;
				case "itemClass":
				case "freeGroupClass":
				case "freeItemClass":
				case "removeItemClass":
					this._refresh(true); // Instant
					this._createItemHandlers();
					break;
			}
			return this;
		},
		_init: function() {
			var self = this;
			this._refresh(function() {
				if(self.options.playlistOptions.autoPlay) {
					self.play(self.current);
				} else {
					self.select(self.current);
				}
			});
		},
		_initPlaylist: function(playlist) {
			this.current = 0;
			this.shuffled = false;
			this.removing = false;
			this.original = $.extend(true, [], playlist); // Copy the Array of Objects
			this._originalPlaylist();
		},
		_originalPlaylist: function() {
			var self = this;
			this.playlist = [];
			// Make both arrays point to the same object elements. Gives us 2 different arrays, each pointing to the same actual object. ie., Not copies of the object.
			$.each(this.original, function(i) {
				self.playlist[i] = self.original[i];
			});
		},
		_refresh: function(instant) {
			/* instant: Can be undefined, true or a function.
			 *	undefined -> use animation timings
			 *	true -> no animation
			 *	function -> use animation timings and excute function at half way point.
			 */
			var self = this;

			if(instant && !$.isFunction(instant)) {
				$(this.cssSelector.playlist + " ul").empty();
				$.each(this.playlist, function(i) {
					$(self.cssSelector.playlist + " ul").append(self._createListItem(self.playlist[i]));
				});
				this._updateControls();
			} else {
				var displayTime = $(this.cssSelector.playlist + " ul").children().length ? this.options.playlistOptions.displayTime : 0;

				$(this.cssSelector.playlist + " ul").slideUp(displayTime, function() {
					var $this = $(this);
					$(this).empty();
					
					$.each(self.playlist, function(i) {
						$this.append(self._createListItem(self.playlist[i]));
					});
					self._updateControls();
					if($.isFunction(instant)) {
						instant();
					}
					if(self.playlist.length) {
						$(this).slideDown(self.options.playlistOptions.displayTime);
					} else {
						$(this).show();
					}
				});
			}
		},
		_createListItem: function(media) {
			var self = this;

			// Wrap the <li> contents in a <div>
			var listItem = "<li class='video-item'><div>";

			// Create remove control
			listItem += "<a href='javascript:;' class='" + this.options.playlistOptions.removeItemClass + "'>&times;</a>";

			// Create links to free media
			if(media.free) {
				var first = true;
				listItem += "<span class='" + this.options.playlistOptions.freeGroupClass + "'>(";
				$.each(media, function(property,value) {
					if($.jPlayer.prototype.format[property]) { // Check property is a media format.
						if(first) {
							first = false;
						} else {
							listItem += " | ";
						}
						listItem += "<a class='" + self.options.playlistOptions.freeItemClass + "' href='" + value + "' tabindex='-1'>" + property + "</a>";
					}
				});
				listItem += ")</span>";
			}

			// The title is given next in the HTML otherwise the float:right on the free media corrupts in IE6/7
			var cls = media.file ? 'z0-playable ' + this.options.playlistOptions.itemClass : 'z0-url';
			listItem += "<a href='javascript:;' class='" + cls + "' tabindex='0' data-url='" + media.url + "'><span>" + media.title + (media.artist ? " <span class='jp-artist'>by " + media.artist + "</span>" : "") +  "</span><img class='video_thumbail' src='" + ( media.thumbnail_src ? media.thumbnail_src : '') + "' >"+"</a>";
			listItem += '<span class="z0-action">';
			//actions
			if( media.file ){
				//size
				listItem += '<span class="z0-size">' + media.size + '</span>';
				listItem += '<a href="' + Azuree.root + 'video/download/' + media.project_id + '/' + media.id + '" class="z0-download">Download</a>';
			}
			//  else {
			// 	//size
			// 	listItem += '<span class="z0-size">-</span>';
			// }
			//delete
			listItem += '<a href="javascript:;" class="z0-delete">' + media.project_id + '/' + media.id + '</a>';
			listItem += '</span>';
			listItem += "</div></li>";

			return listItem;
		},
		_createItemHandlers: function() {
			var self = this;
			// Create live handlers for the playlist items
			$(this.cssSelector.playlist).off("click", "a." + this.options.playlistOptions.itemClass).on("click", "a." + this.options.playlistOptions.itemClass, function(e) {
				e.preventDefault();
				var index = $(this).parent().parent().index();
				if(self.current !== index) {
					self.play(index);
				} else {
					$(self.cssSelector.jPlayer).jPlayer("play");
				}
				self.blur(this);
			});

			// Create live handlers that disable free media links to force access via right click
			$(this.cssSelector.playlist).off("click", "a." + this.options.playlistOptions.freeItemClass).on("click", "a." + this.options.playlistOptions.freeItemClass, function(e) {
				e.preventDefault();
				$(this).parent().parent().find("." + self.options.playlistOptions.itemClass).click();
				self.blur(this);
			});

			// Create live handlers for the remove controls
			$(this.cssSelector.playlist).off("click", "a." + this.options.playlistOptions.removeItemClass).on("click", "a." + this.options.playlistOptions.removeItemClass, function(e) {
				e.preventDefault();
				var index = $(this).parent().parent().index();
				self.remove(index);
				self.blur(this);
			});

			$(this.cssSelector.playlist).off("click", "a.z0-delete").on('click', 'a.z0-delete', function(e){
				e.preventDefault();
				if( confirm(_translate['delete?']) ){
					location.href = Azuree.root + 'video/delete/' + $(this).text();
				}
				return false;
			});

			$(this.cssSelector.playlist).off("click", "a.z0-url").on('click', 'a.z0-url', function(e){
				e.preventDefault();
				window.open($(this).data('url'), '_blank');
				return false;
			});
		},
		_updateControls: function() {
			if(this.options.playlistOptions.enableRemoveControls) {
				$(this.cssSelector.playlist + " ." + this.options.playlistOptions.removeItemClass).show();
			} else {
				$(this.cssSelector.playlist + " ." + this.options.playlistOptions.removeItemClass).hide();
			}

			if(this.shuffled) {
				$(this.cssSelector.jPlayer).jPlayer("addStateClass", "shuffled");
			} else {
				$(this.cssSelector.jPlayer).jPlayer("removeStateClass", "shuffled");
			}
			if($(this.cssSelector.shuffle).length && $(this.cssSelector.shuffleOff).length) {
				if(this.shuffled) {
					$(this.cssSelector.shuffleOff).show();
					$(this.cssSelector.shuffle).hide();
				} else {
					$(this.cssSelector.shuffleOff).hide();
					$(this.cssSelector.shuffle).show();
				}
			}
		},
		_highlight: function(index) {
			if(this.playlist.length && index !== undefined) {
				$(this.cssSelector.playlist + " .jp-playlist-current").removeClass("jp-playlist-current");
				$(this.cssSelector.playlist + " li:nth-child(" + (index + 1) + ")").addClass("jp-playlist-current").find(".jp-playlist-item").addClass("jp-playlist-current");
				var this_item = this.playlist[index];
				var vfooter = ''+
					'<div class="video-footer clear-fix">'+
						'<div class="left">'+
							'<p class="file-name">'+
								this_item.title +
							'</p>'+
							'<p class="file-size">'+
								this_item.size +
							'</p>'+
						'</div>'+
						'<div class="right">'+
							'<a title="<?php __('Download') ?>" href="<?php echo $html->url(array( 'controller' => $this->params['controller'], 'action' => 'download'))?>/' + this_item.project_id + '/' + this_item.id + '" class="wd-download" title="<?php __('Download') ?>"></a>';
							
				<?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) { ?>
								vfooter += '<a href="<?php echo $html->url(array( 'controller' => $this->params['controller'], 'action' => 'delete'))?>/' + this_item.project_id + '/' + this_item.id + '" title="<?php __('Delete') ?>" class="wd-delete wd-hover-advance-tooltip" onclick="return confirm(\'<?php __('Delete?') ?>\')"></a>';
				<?php } ?> 
				vfooter += ''+
						'</div>'+
						'<div style="clear:both;"></div>'+
					'</div>';
				
				$(this.cssSelector.details).html(vfooter);
				// console.log( this.cssSelector.details, index, this.playlist );
			}
		},
		setPlaylist: function(playlist) {
			this._initPlaylist(playlist);
			this._init();
		},
		add: function(media, playNow) {
			$(this.cssSelector.playlist + " ul").append(this._createListItem(media)).find("li:last-child").hide().slideDown(this.options.playlistOptions.addTime);
			this._updateControls();
			this.original.push(media);
			this.playlist.push(media); // Both array elements share the same object pointer. Comforms with _initPlaylist(p) system.

			if(playNow) {
				this.play(this.playlist.length - 1);
			} else {
				if(this.original.length === 1) {
					this.select(0);
				}
			}
		},
		remove: function(index) {
			var self = this;

			if(index === undefined) {
				this._initPlaylist([]);
				this._refresh(function() {
					$(self.cssSelector.jPlayer).jPlayer("clearMedia");
				});
				return true;
			} else {

				if(this.removing) {
					return false;
				} else {
					index = (index < 0) ? self.original.length + index : index; // Negative index relates to end of array.
					if(0 <= index && index < this.playlist.length) {
						this.removing = true;

						$(this.cssSelector.playlist + " li:nth-child(" + (index + 1) + ")").slideUp(this.options.playlistOptions.removeTime, function() {
							$(this).remove();

							if(self.shuffled) {
								var item = self.playlist[index];
								$.each(self.original, function(i) {
									if(self.original[i] === item) {
										self.original.splice(i, 1);
										return false; // Exit $.each
									}
								});
								self.playlist.splice(index, 1);
							} else {
								self.original.splice(index, 1);
								self.playlist.splice(index, 1);
							}

							if(self.original.length) {
								if(index === self.current) {
									self.current = (index < self.original.length) ? self.current : self.original.length - 1; // To cope when last element being selected when it was removed
									self.select(self.current);
								} else if(index < self.current) {
									self.current--;
								}
							} else {
								$(self.cssSelector.jPlayer).jPlayer("clearMedia");
								self.current = 0;
								self.shuffled = false;
								self._updateControls();
							}

							self.removing = false;
						});
					}
					return true;
				}
			}
		},
		select: function(index) {
			index = (index < 0) ? this.original.length + index : index; // Negative index relates to end of array.
			if(0 <= index && index < this.playlist.length) {
				this.current = index;
				this._highlight(index);
				$(this.cssSelector.jPlayer).jPlayer("setMedia", this.playlist[this.current]);
			} else {
				this.current = 0;
			}
		},
		play: function(index) {
			index = (index < 0) ? this.original.length + index : index; // Negative index relates to end of array.
			if(0 <= index && index < this.playlist.length) {
				if(this.playlist.length) {
					this.select(index);
					$(this.cssSelector.jPlayer).jPlayer("play");
				}
			} else if(index === undefined) {
				$(this.cssSelector.jPlayer).jPlayer("play");
			}
		},
		pause: function() {
			$(this.cssSelector.jPlayer).jPlayer("pause");
		},
		next: function() {
			var index = (this.current + 1 < this.playlist.length) ? this.current + 1 : 0;

			if(this.loop) {
				// See if we need to shuffle before looping to start, and only shuffle if more than 1 item.
				if(index === 0 && this.shuffled && this.options.playlistOptions.shuffleOnLoop && this.playlist.length > 1) {
					this.shuffle(true, true); // playNow
				} else {
					this.play(index);
				}
			} else {
				// The index will be zero if it just looped round
				if(index > 0) {
					this.play(index);
				}
			}
			return index;
		},
		previous: function() {
			var index = (this.current - 1 >= 0) ? this.current - 1 : this.playlist.length - 1;

			if(this.loop && this.options.playlistOptions.loopOnPrevious || index < this.playlist.length - 1) {
				this.play(index);
			}
			return index;
		},
		shuffle: function(shuffled, playNow) {
			var self = this;

			if(shuffled === undefined) {
				shuffled = !this.shuffled;
			}

			if(shuffled || shuffled !== this.shuffled) {

				$(this.cssSelector.playlist + " ul").slideUp(this.options.playlistOptions.shuffleTime, function() {
					self.shuffled = shuffled;
					if(shuffled) {
						self.playlist.sort(function() {
							return 0.5 - Math.random();
						});
					} else {
						self._originalPlaylist();
					}
					self._refresh(true); // Instant

					if(playNow || !$(self.cssSelector.jPlayer).data("jPlayer").status.paused) {
						self.play(0);
					} else {
						self.select(0);
					}

					$(this).slideDown(self.options.playlistOptions.shuffleTime);
				});
			}
		},
		blur: function(that) {
			if($(this.cssSelector.jPlayer).jPlayer("option", "autoBlur")) {
				$(that).blur();
			}
		}
	};
})(jQuery);

/* End Playlist Object for the jPlayer Plugin */
</script>
