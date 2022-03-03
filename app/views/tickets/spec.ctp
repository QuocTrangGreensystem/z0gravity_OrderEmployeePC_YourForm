<?php
echo $this->Html->script(array(
	'jquery.form',
	'tinymce/tinymce.min',
	'multipleUpload/plupload.full.min',
	'multipleUpload/jquery.plupload.queue-default',
	'qtip/jquery.qtip',
	'jquery.fancybox.pack',
	'jquery.validation.min'
));
echo $this->Html->css(array(
	'multipleUpload/jquery.plupload.queue',
	'/js/qtip/jquery.qtip',
	'ticket',
	'jquery.fancybox',
	'editor'
));
//format
App::import('vendor', 'str_utility');
$ticket['delivery_date'] = $ticket['delivery_date'] ? str_utility::convertToVNDate($ticket['delivery_date']) : '';
?>

<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project ticket">
				<div class="wd-title">
					<?php echo $this->Session->flash() ?>
					<!-- buttons list -->
				</div>
				<?php echo $this->Form->create('Ticket', array('action' => 'update_info')) ?>
				<?php echo $this->Validation->bind('Ticket'); ?>
				<div id="the-ticket">
					<table id="ticket-fields" class="display">
						<tr>
							<th><?php __('ID') ?></th>
							<th><?php __('Name') ?></th>
							<th><?php __('Type') ?></th>
							<th><?php __('Priority') ?></th>
							<th><?php __('Subscribe') ?></th>
							<th><?php __('Company') ?></th>
						</tr>
						<tr>
							<td><?php echo $ticket['id'] ?></td>
							<td>
								<?php echo $ticket['name'] ?>
							</td>
							<td>
								<?php echo $metas['type'][$ticket['type_id']] ?>
							</td>
							<td>
								<?php echo $metas['priority'][$ticket['priority_id']] ?>
							</td>
							<td>
								<?php echo $this->Form->input('subscribe', array(
									'type' => 'checkbox',
									'value' => '1',
									'checked' => $is_subscribed,
									'disabled' => 'disabled',
									'label' => false
								)) ?>
							</td>
							<td>
								<?php echo $is_external ? $external_companies[$cid] : $company_name ?>
							</td>
						</tr>
						<tr>
							<th><?php __('Status') ?></th>
							<th><?php __('Affected to') ?></th>
							<th><?php __('Delivery date') ?></th>
							<th><?php __('Function') ?></th>
							<th><?php __('Version') ?></th>
							<th><?php __('Opened by') ?></th>
						</tr>
						<tr>
							<td>
								<?php echo $statuses[$ticket['ticket_status_id']]['name'] ?>
							</td>
							<td>
								<?php foreach($affections as $p): ?>
								<span class="separator"><?php __(Inflector::humanize($p)) ?></span>
								<?php endforeach ?>
							</td>
							<td>
								<?php echo $ticket['delivery_date'] ?>
							</td>
							<td>
								<?php echo $metas['function'][$ticket['function_id']] ?>
							</td>
							<td>
								<?php echo $metas['version'][$ticket['version_id']] ?>
							</td>
							<td>
								<div style="vertical-align: middle; display: inline-block; text-align: left;min-width: 180px">
									<img src="<?php echo $this->UserFile->avatar($ticket['employee_id'], 'large') ?>" class="ticket-avatar" style="float: left; margin-right: 10px" alt="">
									<b style="vertical-align: middle;">
										<?php echo $resources[$ticket['employee_id']] ?>
									</b><br/>
									<i>(<?php echo $this->Time->format('H:i, d-m-Y', $ticket['created']) ?>)</i>
								</div>
							</td>
						</tr>
					</table>
					<div id="ticket-info" class="ticket-section">
						<h3 class="ticket-header">
							<?php __('Description') ?>
							<div class="toolbar">
								<?php if( $can_update ): ?>
								<!-- <span id="edit-content" onclick="toggleEditor(true)"></span> -->
								<?php endif ?>
							</div>
						</h3>
						<div class="ticket-wrapper">
							<div id="ticket-content" class="mce-content-body">
								<?php echo $ticket['content'] ?>
							</div>
						</div>
						<textarea id="ticket-editor"></textarea>
						<div id="btn-list">
							<button type="button" class="btn-text ticket-btn" style="margin-top: 15px" id="submit-content">
								<img src="/img/ui/blank-save.png" alt="">
								<span><?php __('Save') ?></span>
							</button>
							<button type="button" class="btn-text btn-red ticket-btn" style="margin-top: 15px" id="cancel-content">
								<img src="/img/ui/blank-reset.png" alt="">
								<span><?php __('Cancel') ?></span>
							</button>
						</div>
					</div>
					<div id="ticket-attachments" class="ticket-section">
						<h3 class="ticket-header">
							<?php __('Documents') ?>
							<div class="toolbar">
								<?php if( $can_update ): ?>
								<span id="open-modal"><i class="icon-doc"></i></span>
								<?php endif ?>
							</div>
						</h3>
						<div id="attachment-images"></div>
						<div id="attachment-documents"></div>
					</div>
				</div>
				<?php echo $this->Form->hidden('redirect', array('value' => '/tickets/view/' . $ticket_id . '/' . $screen)) ?>
				<?php echo $this->Form->end() ?>

			</div>
		</div>
	</div>
</div>
<div id="upload-dialog" title="<?php __('Upload files') ?>">
	<div id="queue">
    </div>
</div>
<!-- templates -->
<div id="tooltip-template" style="display: none">
	<div class="tooltip-action">
		<b class="name"></b>
		<i class="size"></i>
		<!-- <span class="history"></span> -->
		<button class="insert" data-id=""><?php __('Insert') ?></button>
		<button class="download" data-id=""><?php __('Download') ?></button>
		<button class="delete" data-id=""><?php __('Delete') ?></button>
	</div>
</div>

<div id="attachment-images-template" style="display: none">
	<div class="attachment image attachment-{id}" data-id="{id}" data-size="{size}" data-name="{name}">
		<a href="{file}" class="thumbnail" rel="gallery1" target="_blank">
			{thumbnail}
		</a>
		<span></span>
	</div>
</div>

<div id="attachment-documents-template" style="display: none">
	<div class="attachment document attachment-{id}" data-id="{id}" data-size="{size}" data-name="{name}">
		<span>{name}</span>
	</div>
</div>

<script>
var id = <?php echo $ticket_id ?>,
	company_id = <?php echo $company_id ?>,
	attachments = <?php echo json_encode($attachments) ?>,
	resources = <?php echo json_encode($resources) ?>,
	templates = {
		image: $('#attachment-images-template').html(),
		doc: $('#attachment-documents-template').html()
	},
	can_update = <?php echo json_encode($can_update) ?>;

tinymce.init({
	selector: '#ticket-editor',
	autoresize_min_height: 300,
	autoresize_bottom_margin: 0,
	// height: 300,
	plugins: [
		'advlist autolink lists link image charmap anchor fullscreen table contextmenu wordcount textcolor colorpicker emoticons imagetools spellchecker fullscreen paste autoresize painter'
	],
	menubar: false,
	toolbar: 'bold italic blockquote forecolor styleselect | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image painter emoticons | fullscreen',
	content_css: [
		// '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
		// '//www.tinymce.com/css/codepen.min.css'
		'/css/editor.css'
	],
	skin: 'z0',
	image_advtab: true,
	language: Azuree.language,
	// use absolute url for image
	relative_urls : false,
	remove_script_host : true,
	convert_urls : true,

	entity_encoding: 'raw',
	entities : '160,nbsp,162,cent,8364,euro,163,pound',
	// inline: true,
	// readonly: true,
	setup: function(ed) {
		ed.on('init', function(e) {
			e.target.hide();
		});

		ed.on('show', function(e){
			var content = $('#ticket-content').html();
			ed.setContent(content);
			ed.save();
		});
	},
	image_caption: true,
	paste_data_images: true,
	automatic_uploads: true,
	images_upload_url: '/user_files/ticket_image_upload/' + id
});

if( Azuree.language == 'fr' ){
	plupload.addI18n({"Stop Upload":"Arrêter l'envoi.","Upload URL might be wrong or doesn't exist.":"L'URL d'envoi est soit erronée soit n'existe pas.","tb":"To","Size":"Taille","Close":"Fermer","Init error.":"Erreur d'initialisation.","Add files to the upload queue and click the start button.":"Ajoutez des fichiers à la file d'attente de téléchargement et appuyez sur le bouton 'Démarrer l'envoi'","Filename":"Fichier","Image format either wrong or not supported.":"Le format d'image est soit erroné soit pas géré.","Status":"État","HTTP Error.":"Erreur HTTP.","Start Upload":"Charger","mb":"Mo","kb":"Ko","Duplicate file error.":"Erreur: Fichier déjà sélectionné.","File size error.":"Erreur de taille de fichier.","N/A":"Non applicable","gb":"Go","Error: Invalid file extension:":"Erreur: Extension de fichier non valide:","Select files":"Sélectionnez les fichiers","%s already present in the queue.":"%s déjà présent dans la file d'attente.","File: %s":"Fichier: %s","b":"o","Uploaded %d/%d files":"%d fichiers sur %d ont été envoyés","Upload element accepts only %d file(s) at a time. Extra files were stripped.":"Que %d fichier(s) peuvent être envoyé(s) à la fois. Les fichiers supplémentaires ont été ignorés.","%d files queued":"%d fichiers en attente","File: %s, size: %d, max file size: %d":"Fichier: %s, taille: %d, taille max. d'un fichier: %d","Drag files here.":"Déposez les fichiers ici.","Runtime ran out of available memory.":"Le traitement a manqué de mémoire disponible.","File count error.":"Erreur: Nombre de fichiers.","File extension error.":"Erreur d'extension de fichier","Error: File too large:":"Erreur: Fichier trop volumineux:","Add Files":"Ajouter"});
}

$(document).ready(function(){
	// fixed
	$('#ticket-fields').follow();

    var listAttachments = {image: {}, 'document': {}};

	$("#queue").pluploadQueue({
		runtimes : 'html5,flash,html4',
		url : '/tickets/upload/' + id,
		dragdrop: true,
		filters : {
			max_file_size : '1000mb',
			mime_types: [
				{title : "Files", extensions : <?php echo json_encode($allowedFiles) ?>}
			]
		},
		flash_swf_url : '/js/moxie.swf',
		multiple_queues: true
	});
    var uploader = $('#queue').pluploadQueue();
	var modal = $('#upload-dialog').dialog({
		autoOpen: false,
		width: 'auto',
		modal: true,
		open: function(e, ui){
			uploader.refresh();
			$('#queue input').css('z-index','99999');
			listAttachments = {image: {}, 'document': {}};
		}
	});
    uploader.bind('FileUploaded', function(inst, fl, res){
    	var f = $.parseJSON(res.response);
    	if( f ){
	    	if( f.thumbnail ){
	    		listAttachments.image[f.id] = f;
	    	} else {
	    		listAttachments['document'][f.id] = f;
	    	}
	    }
    });
    uploader.unbind('UploadComplete');
    uploader.bind('UploadComplete', function(inst){
    	modal.dialog('close');
    	buildAttachments(listAttachments, true);
    	listAttachments = {image: {}, 'document': {}};
    	uploader.refresh();
    });
	$('#open-modal').click(function(){
		modal.dialog('open');
	});

	buildAttachments();

	var saving;

	//submit content
	$('#submit-content').click(function(){
		if( saving )return false;
		saving = true;
		var editor = tinyMCE.get('ticket-editor');
		setLoadding('ticket-editor', true);
		var content = $.trim(editor.getContent());
		$.ajax({
			url: '/tickets/update_content/',
			type: 'POST',
			data: {
				data: {
					id: id,
					content: content
				}
			},
			success: function(){
				//hide editor
				toggleEditor(false);
				editor.setContent('');
				editor.save();
				$('#ticket-content').html(content);
			},
			error: function(){
				alert(<?php echo json_encode(__('Problem while saving', true)) ?>);
			},
			complete: function(){
				saving = false;
				setLoadding('ticket-editor', false);
			}
		})
	});
	$('#cancel-content').click(function(){
		if( saving )return false;
		var editor = tinyMCE.get('ticket-editor');
		if( editor.isDirty() ){
			if( !confirm(<?php echo json_encode(__('Discard changes?', true)) ?>) ){
				return false;
			}
		}
		editor.setContent('');
		editor.save();
		toggleEditor(false);
	});

	$('#ticket-info').dblclick(function(){
		if( can_update ){
			toggleEditor(true);
		}
	});
});

function toggleEditor(t){
	var ed = tinymce.editors['ticket-editor'];
	if( t ){
		$('#ticket-content').hide();
		ed.show();
		$('.ticket-btn').show();
	} else {
		ed.hide();
		$('#ticket-content').show();
		$('.ticket-btn').hide();
	}
}

function bindTip(){
	$('.attachment span').qtip('destroy', true);
	$('.attachment span').qtip({
		show: 'click',
        hide: 'unfocus',
		content: {
			text: function(e, api){
				var me = $(e.target).closest('.attachment'),
					attachment_id = me.data('id'),
					type = me.hasClass('image') ? 'image' : 'document';
				var content = $($('#tooltip-template').html());
				content.find('b').text(me.data('name'));
				content.find('i').text(me.data('size'));
				content.find('.download').on('click', function(){
					window.open('/ticket_attachments/download/' + id + '/' + attachment_id, '_blank');
					$('.attachment span').qtip('hide');
				});
				content.find('.delete').on('click', function(){
					if( confirm(<?php echo json_encode(__('Delete?', true)) ?>) ){
						$('.attachment-' + attachment_id).css('opacity', 0.5);
						$.ajax({
							url: '/ticket_attachments/delete/' + id + '/' + attachment_id,
							success: function(r){
								if( r ){
									$('.attachment-' + attachment_id).fadeOut(function(){
										$(this).remove();
									});
								}
							}
						});
					}
					$('.attachment span').qtip('hide');
					//location.href = '/ticket_attachments/delete/' + id + '/' + attachment_id;
				});
				if( type == 'image' ){
					content.find('.insert').on('click', function(){
						if( $('.ticket-btn').is(':visible') ){
							var editor = tinymce.get('ticket-content'),
								url = <?php echo $this->UserFile->ticketImage('company_id', 'id', 'me.data(\'name\')') ?>;
							editor.execCommand('mceInsertContent', false, '<img src="' + url + '" alt="">');
						}
						$('.attachment span').qtip('hide');
					});
				} else {
					content.find('.insert').remove();
				}
				if( !can_update ){
					content.find('.delete,.insert').remove();
				}
				return content;
			}
		},
		position: {
			my: 'bottom center',
			at: 'top center'
		},
		style: {
			classes: 'qtip-shadow qtip-light'
		}
	});
	$('#attachment-images a').fancybox();
	$('.datepicker').datepicker({
		dateFormat: 'dd-mm-yy'
	});
}

function buildAttachments(a, prepend){
	if( !a )a = attachments;
	//build image
	if( a.image ){
		var html = '';
		$.each(a.image, function(aid, f){
			html += prepareAttachment(f);
		});
		if( !prepend ){
			$('#attachment-images').append(html);
		} else {
			$('#attachment-images').prepend(html);
		}
	}
	//build doc
	if( a['document'] ){
		var html = '';
		$.each(a['document'], function(aid, f){
			html += prepareAttachment(f);
		});
		if( !prepend ){
			$('#attachment-documents').append(html);
		} else {
			$('#attachment-documents').prepend(html);
		}
	}

	bindTip();
}

function prepareAttachment(f){
	var html = '';
	f.name = f.file;
	f.size = fsize(f.size);
	if( f.thumbnail ){
		var content = templates.image;
		f.file = <?php echo $this->UserFile->ticketImage('company_id', 'id', 'f.name') ?>;
		var thumbnail = <?php echo $this->UserFile->ticketImage('company_id', 'id', 'f.thumbnail') ?>;
		f.thumbnail = '<img src="' + thumbnail + '" alt="">';
		html = replace(f, content);
	} else {
		var content = templates.doc;
		html = replace(f, content);
	}
	return html;
}

//utilities

function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceAll(find, replace, str) {
  return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

function replace(obj, str){
	$.each(obj, function(key, value){
		str = replaceAll('{' + key + '}', value, str);
	});
	return str;
}

function fsize(size) {
	size = parseInt(size);
    var i = Math.floor( Math.log(size) / Math.log(1024) );
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
}

function setLoadding(id, status, inline){
	var e = $(tinymce.get(id).getContainer());
	if( inline ){
		e = $('#' + id);
	}
	var w = e.width(),
		h = e.height();
	var overlay = $('#to-' + id);
	if( !overlay.length ){
		overlay = $('<div id="to-' + id + '" class="overlay"></div>').appendTo('body');
	}
	overlay.width(w)
		.height(h)
		.css({
			top: e.offset().top,
			left: e.offset().left
		});
	if( status ){
		overlay.show();
	} else {
		overlay.hide();
	}
}
</script>
