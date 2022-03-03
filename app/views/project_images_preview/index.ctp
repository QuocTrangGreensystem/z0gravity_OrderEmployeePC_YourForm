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

/* 
* Edit by Dai Huynh 
* Remove Flexslider:
*       jquery.flexslider-min.js
*       flexslider.css
* Add Slick Slider: 
*       slick.min.js
*       Slick.css
*       slick-theme.css
*/
    echo $html->script(array(
        'multipleUpload/plupload.full.min',
        'multipleUpload/jquery.plupload.queue-default',
        'slick.min',
        'jquery.fancybox.pack'
    ));
 ?>
<?php echo $html->script('dropzone.min'); ?>

<?php echo $html->css('dropzone.min'); ?>
<?php echo $html->css('preview/project_images'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('slick'); ?>
<?php echo $html->css('slick-theme'); ?>
<?php echo $html->css(array('jquery.ui.custom', 'multipleUpload/jquery.plupload.queue', 'jquery.fancybox')); ?>
<style>
	.wd-title{
		height: 0;
		margin: 0;
		position: relative;
		z-index: 2;
	}
</style>

<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
                    <a href="#" id="open-modal" class="btn-add smart-phone <?php if(empty($images)) echo 'rotate';?>" title="<?php echo __('Upload files') ?>">
                        
                    </a>
                    <div style="clear: both;"></div>
                    <?php endif; ?>
                </div>
                <div id="upload-dialog" <?php if(!empty($images)) echo 'style="display: none"';?> title="<?php __('Upload files') ?>">
                    <form id="upload-widget" method="post" action="/project_images_preview/upload/<?php echo $company_id ?>/<?php echo $project_id ?>" class="dropzone" value="" >
                    </form>
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
				<div class="wd-section">
					<div id="slider" class="flexslider">
							<?php foreach ($images as $image) {
								if( $image['ProjectImage']['type'] != 'image' )
									continue;
								$url = $link = $this->Html->url(array('action' => 'attachment', $project_id, $image['ProjectImage']['id'], '?' => array('sid' => $api_key)), true);
								if( file_exists($uploadFolder . $company_id . '/' . $project_id . '/l_' . $image['ProjectImage']['file']) ){
									$url = $this->Html->url(array('action' => 'attachment', $project_id, $image['ProjectImage']['id'], 'l_','?' => array('sid' => $api_key)), true);
								}
								?>
								<div class="image-present">
									<div class="image-frame">
										<img data-id="<?php echo $image['ProjectImage']['id'] ?>" src="<?php echo $url; ?>"  width="800" height="450" alt="">
									 </div>
									<a href="<?php echo $link ?>" class="fancy" rel="gallery1" id="on-image-expand-btn" class="btn btn-frame"></a>
									<div class="image-footer clear-fix">
										<div class="left">
											<p class="file-name">
											<?php if( get_mime_type($image['ProjectImage']['file']) == 'application/pdf' ): ?>
												<a href="https://docs.google.com/viewer?url=<?php echo urlencode($html->url('/project_images/show/' . $project_id . '/' . $image['ProjectImage']['id'])) ?>" target="_blank" class="show-window" title="<?php __('View on Google Docs') ?>"><?php echo $image['ProjectImage']['file'] ?></a>
											<?php else: ?>
												<?php echo $image['ProjectImage']['file'] ?>
											<?php endif ?>
											</p>
											<p class="file-size">
												<?php echo filesize_formatted($image['ProjectImage']['size']) ?>
											</p>
										</div>
										<div class="right">
											<a title="<?php __('Download') ?>" href="<?php echo $html->url('/project_images/download/' . $image['ProjectImage']['id']) ?>" class="wd-download" title="<?php __('Download') ?>"></a>
										<?php if((!empty($canModified) && !$_isProfile) || ($_isProfile && $_canWrite)) : ?>
											<a href="<?php echo $html->url('/project_images/delete/' . $image['ProjectImage']['id'] . '/' . $project_id) ?> ?>" title="<?php __('Delete') ?>" class="wd-delete wd-hover-advance-tooltip" onclick="return confirm('<?php __('Delete?') ?>')" title="<?php __('Delete') ?>"></a>
										<?php endif; ?>
										</div>
										<div style="clear:both;"></div>
									</div>
								</div>
							<?php } ?>
					</div>
					<div id="carousel" class="flexslider">
							<?php
							foreach ($images as $image) {
								if( $image['ProjectImage']['type'] != 'image' )
									continue;
								$url = $this->Html->url(array('action' => 'attachment', $projectName['Project']['id'], $image['ProjectImage']['id'], 'r_', '?' => array('sid' => $api_key)), true);
							?>
								<div>
									<img src="<?php echo $url ?>" alt="">
								</div>
							<?php } ?>
					</div>
				</div>
                <?php endif ?>
            </div>
            </div></div>
        </div>
    </div>
</div>

<!-- modal -->

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

    var myDropzone = new Dropzone("#upload-widget", {
        acceptedFiles: ".jpeg,.jpg,.png,.gif"
    });
    myDropzone.on("success", function(file) {
        myDropzone.removeFile(file);
        location.reload();
    });
    
    $('#open-modal').click(function(){
        $(this).toggleClass('rotate')
        $('#upload-dialog').toggle();
    });
    $("a.fancy").fancybox({
        type: 'image',
    });

    /* Use Slick Slider 
    * By Dai Huynh
    */
    // Slider
    var slick_slider = $('#slider').slick({
        infinite: true,
        speed: 600,
        lazyLoad: 'ondemand',
        fade: true,
        dots: false,
        asNavFor: '#carousel',
        prevArrow: '<button type="button" class="slick-prev"><i class="icon-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="icon-arrow-right"></i></button>',
        responsive:[
            {
                breakpoint: 1024,
                settings: {
                    dots: true
                }
            }

        ]
    });

    // Nav
    $('#carousel').slick({
        infinite: true,
        slidesToShow: $('#carousel >div').length,
        // slidesToScroll: 3,
        speed: 600,
        lazyLoad: 'ondemand',
       asNavFor: '#slider',
        vertical: true,
        verticalSwiping: true,
        dots: false,
        focusOnSelect: true,
        arrows: false, 
        responsive:[
            {
                breakpoint: 1200,
                settings: {
                    vertical: false,
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
        ] 
    });

        /*
        responsive: [
            
            {
                // small than 1024 (<=1023)
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 768,
                settings: "unslick"
            }
        ]*/

})(jQuery);
// var myDropzone = new Dropzone("#upload-widget");
var $listDimencision = <?php echo json_encode($listDimencision) ?>;
var img = $('#slider').find('img');
setTimeout(function(){
    $.each(img, function(ind, val){
        var _id = $(val).data('id');
        var _w = ($listDimencision[_id]['width']||0);
		var _h = ($listDimencision[_id]['height']||0);
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
