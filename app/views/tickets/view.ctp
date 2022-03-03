<?php
echo $this->Html->script(array(
	'jquery.form',
	'tinymce/tinymce.min',
	'multipleUpload/plupload.full.min',
	'multipleUpload/jquery.plupload.queue-default',
	'qtip/jquery.qtip',
	'dropzone.min',
	'jquery.fancybox.pack',
	'jquery.validation.min',
	'multiple-select'
));
echo $this->Html->css(array(
	'multipleUpload/jquery.plupload.queue',
	'/js/qtip/jquery.qtip',
	'dropzone.min',
	'ticket',
	'jquery.fancybox',
	'multiple-select',
	'editor',
	'preview/component',
	'preview/datepicker-new',
));
//format
App::import('vendor', 'str_utility');
$ticket['delivery_date'] = $ticket['delivery_date'] ? str_utility::convertToVNDate($ticket['delivery_date']) : '';

?>
<style>
	img {
		max-width: 100%;
		height: auto;
	}

	.border--bottom-none {
		border-bottom: none;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project ticket">
				<div class="wd-title">
					<?php echo $this->Session->flash() ?>
					<!-- buttons list -->
					<?php if ($can_update) : ?>
						<button type="button" class="btn-ticket btn-save" style="margin-top: 5px" id="update-button">
							<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px">
								<path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path>
							</svg>
						</button>
					<?php endif ?>
				</div>
				<?php echo $this->Form->create('Ticket', array('action' => 'update_info')) ?>
				<?php echo $this->Validation->bind('Ticket'); ?>
				<div id="the-ticket">
					<table id="ticket-fields" class="display">
						<tr>
							<th><?php __('ID') ?></th>
							<th><?php __('Description') ?></th>
							<th><?php __('Type') ?></th>
							<th><?php __('Priority') ?></th>
							<?php if ($profile['role'] != 'customer') : ?>
								<th><?php __('Subscribe') ?></th>
							<?php endif ?>
							<th><?php __('Company') ?></th>
						</tr>
						<tr>
							<td><?php echo $ticket['id'] ?></td>
							<td>
								<?php echo $this->Form->hidden('id', array('value' => $ticket_id)) ?>
								<?php if ($can_update) : ?>
									<?php echo $this->Form->input('name', array(
										'value' => $ticket['name'],
										'label' => false,
									)) ?>
								<?php else : ?>
									<?php echo $ticket['name'] ?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($can_update) : ?>
									<?php echo $this->Form->input('type_id', array(
										'type' => 'select',
										'options' => $metas['type'],
										'value' => $ticket['type_id'],
										'label' => false,
										'empty' => __('-- Select -- ', true)
									)) ?>
								<?php else : ?>
									<?php if (!!$ticket['type_id'] && !!$metas['type'][$ticket['type_id']]) echo $metas['type'][$ticket['type_id']];
									else echo ''; ?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($can_update) : ?>
									<?php echo $this->Form->input('priority_id', array(
										'type' => 'select',
										'options' => $metas['priority'],
										'value' => $ticket['priority_id'],
										'label' => false,
										'empty' => __('-- Select -- ', true)
									)) ?>
								<?php else : ?>
									<?php if (!!$ticket['priority_id'] && !!$metas['priority'][$ticket['priority_id']]) echo $metas['priority'][$ticket['priority_id']];
									else echo ''; ?>
								<?php endif ?>
							</td>
							<?php if ($profile['role'] != 'customer') : ?>
								<td>
									<?php
									$subscribed = !empty($is_subscribed) ? $is_subscribed : 0;
									$checked = !empty($is_subscribed) ? 'checked' : '';
									echo $this->Form->input('subscribe', array(
										'type' => 'checkbox',
										'value' => $subscribed,
										'checked' => $checked,
										'label' => true
									)) ?>
								</td>
							<?php endif ?>

							<td>
								<?php if ($profile['role'] == 'customer') {

									$subscribed = !empty($is_subscribed) ? $is_subscribed : 0;
									$checked = !empty($is_subscribed) ? 'checked' : '';
									echo $this->Form->input('subscribe', array(
										'type' => 'checkbox',
										'class' => 'hidden',
										'value' => $subscribed,
										'checked' => $checked,
										'label' => false,
									));
								}
								?>

								<?php if ($can_update && $profile['role'] == 'developer') : ?>
									<div class="input select">
										<select name="data[Ticket][company_id]" id="company-select">
											<option value="Company-<?php echo $company_id ?>" <?php if ($ticket['company_model'] == 'Company' && $ticket['company_id'] == $company_id) echo 'selected' ?>><?php echo $company_name ?></option>
											<?php foreach ($external_companies as $id => $name) { ?>
												<option value="External-<?php echo $id ?>" <?php if ($ticket['company_model'] == 'External' && $ticket['company_id'] == $id) echo 'selected' ?>><?php echo $name ?>
													<!--  (<?php __('External') ?>) -->
												</option>
											<?php } ?>
										</select>
									</div>
								<?php else : ?>
									<?php
									if ($is_external) {
										echo $this->Form->hidden('company_id', array('value' => 'External-' . $cid));
									} else {
										echo $this->Form->hidden('company_id', array('value' => 'Company-' . $company_id));
									}
									?>
									<?php echo $is_external ? $external_companies[$cid] : $company_name ?>
								<?php endif ?>
							</td>
						</tr>
						<tr>
							<th><?php __('Affected to') ?></th>
							<th><?php __('Status') ?></th>
							<?php if ($profile['role'] != 'customer') : ?>
								<th><?php __('Delivery date') ?></th>
							<?php endif ?>
							<th><?php __('Function') ?></th>
							<th><?php __('Version') ?></th>
							<th><?php __('Opened by') ?></th>
						</tr>
						<tr>
							<td>
								<?php foreach ($affections as $p) : ?>
									<span class="separator"><?php __(Inflector::humanize($p)) ?></span>
								<?php endforeach ?>
							</td>
							<td>
								<?php if ($can_update) : ?>
									<select name="data[Ticket][ticket_status_id]" id="dataTicketStatus">
										<?php foreach ($statuses as $id => $status) {
											if (!isset($visible_statuses[$id]) && ($ticket['ticket_status_id'] != $id)) continue;
										?>

											<option value="<?php echo $id ?>" <?php if ($ticket['ticket_status_id'] == $id) echo 'selected' ?> <?php if (!isset($visible_statuses[$id])) echo 'disabled' ?>><?php echo $status['name'] ?></option>
										<?php } ?>
									</select>
									<img src="/img/rebuild.jpg" id="reset-status" style="vertical-align: middle; cursor: pointer" title="<?php __('Reset') ?>">
								<?php else : ?>
									<?php echo $statuses[$ticket['ticket_status_id']]['name'] ?>
								<?php endif ?>
								<?php echo $this->Form->hidden('old_status_id', array('value' => $ticket['ticket_status_id'])) ?>
							</td>

							<?php if ($profile['role'] != 'customer') : ?>
								<td>
									<?php if ($can_update && $profile['role'] == 'developer') : ?>
										<div class="delivery-date">
											<?php echo $this->Form->input('delivery_date', array(
												'value' => $ticket['delivery_date'],
												'label' => false,
												'class' => 'datepicker',
												'type' => 'text',
												'autocomplete' => 'off'
											)) ?>
										</div>
									<?php else : ?>
										<?php echo $ticket['delivery_date'] ?>
									<?php endif ?>
								</td>
							<?php endif ?>
							<td>
								<?php if ($can_update) : ?>
									<?php echo $this->Form->input('function_id', array(
										'type' => 'select',
										'options' => $metas['function'],
										'value' => $ticket['function_id'],
										'label' => false,
										'empty' => __('-- Select -- ', true)
									)) ?>
								<?php else : ?>
									<?php if (!!$ticket['function_id'] && !!$metas['function'][$ticket['function_id']]) echo $metas['function'][$ticket['function_id']];
									else echo ''; ?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($can_update) : ?>
									<?php echo $this->Form->input('version_id', array(
										'type' => 'select',
										'options' => $metas['version'],
										'value' => $ticket['version_id'],
										'label' => false,
										'empty' => __('-- Select -- ', true)
									)) ?>
								<?php else : ?>
									<?php if (!!$ticket['version_id'] && !!$metas['version'][$ticket['version_id']]) echo $metas['version'][$ticket['version_id']];
									else echo ''; ?>
								<?php endif ?>
							</td>
							<td>
								<div style="text-align: left;min-width: 180px; margin-left: 10px;">
									<img src="<?php echo $this->UserFile->avatar($ticket['employee_id'], 'large') ?>" class="ticket-avatar" style="margin-right: 10px" alt="">
									<div class="avatar-top">
										<b>
											<?php echo !empty($resources[$ticket['employee_id']]) ? $resources[$ticket['employee_id']] : ''; ?>
										</b>
										<span>(<?php echo $this->Time->format('H:i, d-m-Y', $ticket['created']) ?>)</span>
									</div>
								</div>
							</td>
						</tr>
					</table>
					<?php if ($profile['role'] != 'customer') : ?>
						<div id="ticket-info" class="ticket-section">
							<h3 class="ticket-header">
								<?php __('Description') ?>
								<div class="toolbar">
									<?php if ($can_update) : ?>
										<!-- <span id="edit-content" onclick="toggleEditor(true)"></span> -->
									<?php endif ?>
								</div>
							</h3>
							<div class="ticket-wrapper">
								<div id="ticket-content" class="mce-content-body">
									<?php echo $ticket['content'] ?>
								</div>
								<textarea id="ticket-editor"></textarea>
							</div>
							<div id="btn-list">
								<button type="button" class="btn-ticket btn-save btn-text ticket-btn" style="margin-top: 15px" id="submit-content">
									<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px">
										<path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path>
									</svg>
									<button type="button" class="btn-ticket btn-text btn-red ticket-btn" style="margin-top: 15px" id="cancel-content">
										<i class="icon-reload"></i>
									</button>
							</div>
						</div>
						<div id="ticket-attachments" class="ticket-section">
							<h3 class="ticket-header">
								<?php __('Documents') ?>
								<div class="toolbar">
									<?php if ($can_update) : ?>
										<span id="open-modal" onclick="openAttachmentDialog.call(this)" data-id=<?php echo $ticket_id; ?>><i class="icon-doc"></i></span>
									<?php endif ?>
								</div>
							</h3>
							<div id="attachment-images"></div>
							<div id="attachment-documents"></div>
							<div id="attachment-link"></div>
						</div>
					<?php endif ?>
				</div>
				<?php echo $this->Form->hidden('redirect', array('value' => '/tickets/view/' . $ticket_id . '/' . $screen)) ?>
				<?php echo $this->Form->end() ?>

				<div id="ticket-comments">
					<h3 class="ticket-header"><?php __('Comments') ?></h3>
					<div id="comment-list">
						<?php echo $this->Form->create(false, array('url' => '/ticket_comments/update', 'id' => 'comment-form')) ?>
						<div class="comment comment-form">
							<img src="<?php echo $this->UserFile->avatar($me, 'large') ?>" alt="" class="ticket-avatar comment-avatar my-avatar">
							<div class="comment-text-wrapper">
								<textarea id="comment-text" cols="30" rows="10"></textarea>
								<button type="submit" class="btn-ticket btn-save" style="margin-top: 10px">
									<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px">
										<path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path>
									</svg>
								</button>
							</div>
							<?php echo $this->Form->hidden('ticket_id', array('value' => $ticket_id)) ?>
						</div>
						<ul id="content-comments">

						</ul>
						<?php echo $this->Form->end() ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->element("paginator"); ?>
<div id="upload-dialog" title="<?php __('Upload files') ?>">
	<div id="queue">
	</div>
</div>
<div id="template_upload" class="template_upload" style="height: auto; width: 320px;">
	<div class="heading">
		<h4><?php echo __('File upload(s)', true) ?></h4>
		<span class="close close-popup"><img title="<?php __('Close') ?>" src="<?php echo $html->url('/img/new-icon/close.png'); ?>" /></span>
	</div>
	<div id="content_comment">
		<div class="append-comment"></div>
	</div>
	<div class="wd-popup">
		<?php
		echo $this->Form->create('Upload', array(
			'type' => 'POST',
			'url' => array('controller' => 'tickets', 'action' => 'upload', $ticket_id)
		));
		?>
		<div class="trigger-upload">
			<div id="upload-popup" method="post" action="/tickets/upload/<?php echo $ticket_id; ?>" class="dropzone" value="">
			</div>
		</div>
		<?php echo $this->Form->input('url', array(
			'class' => 'not_save_history',
			'label' => array(
				'class' => 'label-has-sub',
				'text' => __('URL Link', true),
				'data-text' => __('(optionnel)', true),
			),
			'type' => 'text',
			'id' => 'newDocURL',
			'placeholder' => __('https://', true)
		));
		?>
		<input type="hidden" name="data[Upload][id]" rel="no-history" value="" id="UploadId">
		<?php echo $this->Form->end(); ?>
	</div>
	<ul class="actions" style="">
		<li><a href="javascript:void(0)" class="cancel"><?php __("Upload Cancel") ?></a></li>
		<li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('Upload Validate') ?></a></li>
	</ul>
</div>
<div class="light-popup"></div>
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

<div id="attachment-link-template" style="display: none">
	<div class="attachment link attachment-{id}" data-id="{id}" data-name="{name}">
		<span><i class="icon-link"></i><a target="_blank" href="{name}">{name}</a></span>
	</div>
</div>

<ul id="comment-template" style="display: none">
	<li class="comment" id="comment-{id}" data-id="{id}">
		<img src="<?php echo $this->UserFile->avatar('{employee_id}', 'large') ?>" alt="" class="ticket-avatar comment-avatar">
		<ul class="comment-actions">
			<li><a href="#" class="reply static" title="<?php __('Reply') ?>"><i class="icon-action-undo"></i></a></li>
			<li><a href="#" class="quote static" title="<?php __('Quote') ?>"><i class="icon-bubble"></i></a></li>
			<li><a class="edit edit-{id}" href="#" title="<?php __('Edit') ?>"><i class="icon-pencil"></i></a></li>
			<li><a class="delete delete-{id}" href="#" title="<?php __('Delete') ?>"><i class="icon-trash"></i></a></li>
		</ul>
		<div class="comment-content">
			<h3 class="comment-author">
				<a href="#comment-{id}">{name}</a>
			</h3>
			<i class="comment-time">
				{time}
			</i>
			<div class="comment-text mce-content-body" id="content-editor-{id}">{content}</div>
			<textarea id="editor-{id}" style="display: none"></textarea>
			<div class="comment-buttons">
				<button type="button" class="btn-ticket btn-save save">
					<svg fill="#6EAF79" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22px" height="36px">
						<path d="M 19.980469 5.9902344 A 1.0001 1.0001 0 0 0 19.292969 6.2929688 L 9 16.585938 L 5.7070312 13.292969 A 1.0001 1.0001 0 1 0 4.2929688 14.707031 L 8.2929688 18.707031 A 1.0001 1.0001 0 0 0 9.7070312 18.707031 L 20.707031 7.7070312 A 1.0001 1.0001 0 0 0 19.980469 5.9902344 z"></path>
					</svg>
				</button>
				<button type="button" class="btn-ticket btn-text btn-red cancel" data-id="{id}">
					<i class="icon-reload"></i>
				</button>
			</div>
		</div>
	</li>
</ul>
<textarea id="comment-editor" style="display: none"></textarea>
<!-- upload -->
<form method="post" action="/user_files/ticket_image_upload/<?php echo $ticket_id ?>" style="width: 0; height: 0; overflow: hidden" enctype="multipart/form-data" id="temp-upload-form">
	<input name="image" type="file" onchange="doUpload.call(this)">
</form>
<script>
	initTinymce();
	var id = <?php echo $ticket_id ?>,
		company_id = <?php echo $company_id ?>,
		me = <?php echo $me ?>,
		attachments = <?php echo json_encode($attachments) ?>,
		resources = <?php echo json_encode($resources) ?>,
		templates = {
			image: $('#attachment-images-template').html(),
			doc: $('#attachment-documents-template').html(),
			link: $('#attachment-link-template').html()
		},
		can_update = <?php echo json_encode($can_update) ?>,
		comments = <?php echo json_encode($comments) ?>;
	console.log(attachments);

	function doUpload() {
		var me = $(this);
		$('#temp-upload-form').ajaxSubmit({
			dataType: 'json',
			success: function(d) {
				var url = d.location,
					win = me.data('win');
				win.document.getElementById(me.data('name')).value = url;
			}
		});
		this.value = '';
	}
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
			'/css/editor.css'
		],
		skin: 'z0',
		image_advtab: true,
		language: Azuree.language,
		// use absolute url for image
		relative_urls: false,
		remove_script_host: true,
		convert_urls: true,

		entity_encoding: 'raw',
		entities: '160,nbsp,162,cent,8364,euro,163,pound',
		// inline: true,
		// readonly: true,
		setup: function(ed) {
			ed.on('init', function(e) {
				e.target.hide();
			});

			ed.on('show', function(e) {
				var content = $('#ticket-content').html();
				ed.setContent(content);
				ed.save();
			});
		},
		image_caption: true,
		paste_data_images: true,
		automatic_uploads: true,
		images_upload_url: '/user_files/ticket_image_upload/' + id,

		file_browser_callback: function(field_name, url, type, win) {
			if (type == 'image') {
				$('#temp-upload-form input').data({
					name: field_name,
					win: win
				}).click();
			}
		}
	});

	function initTinymce() {
		// comment form
		tinymce.init({
			selector: '#comment-text',
			autoresize_min_height: 200,
			autoresize_bottom_margin: 0,
			plugins: [
				'advlist autolink lists link image charmap anchor fullscreen table contextmenu textcolor colorpicker emoticons imagetools spellchecker fullscreen paste autoresize painter'
			],
			menubar: false,
			image_advtab: true,
			// statusbar: false,
			toolbar: 'bold italic blockquote forecolor styleselect | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist | link image painter emoticons | fullscreen',
			content_css: [
				'/css/editor.css'
			],
			skin: 'z0',
			language: Azuree.language,

			// use absolute url for image
			relative_urls: false,
			remove_script_host: true,
			convert_urls: true,

			entities: '160,nbsp,162,cent,8364,euro,163,pound',
			entity_encoding: 'raw',
			image_caption: true,

			paste_data_images: true,
			automatic_uploads: true,
			images_upload_url: '/user_files/ticket_image_upload/' + id,

			file_browser_callback: function(field_name, url, type, win) {
				if (type == 'image') {
					$('#temp-upload-form input').data({
						name: field_name,
						win: win
					}).click();
				}
			}
		});

		if (Azuree.language == 'fr') {
			plupload.addI18n({
				"Stop Upload": "Arrêter l'envoi.",
				"Upload URL might be wrong or doesn't exist.": "L'URL d'envoi est soit erronée soit n'existe pas.",
				"tb": "To",
				"Size": "Taille",
				"Close": "Fermer",
				"Init error.": "Erreur d'initialisation.",
				"Add files to the upload queue and click the start button.": "Ajoutez des fichiers à la file d'attente de téléchargement et appuyez sur le bouton 'Démarrer l'envoi'",
				"Filename": "Fichier",
				"Image format either wrong or not supported.": "Le format d'image est soit erroné soit pas géré.",
				"Status": "État",
				"HTTP Error.": "Erreur HTTP.",
				"Start Upload": "Charger",
				"mb": "Mo",
				"kb": "Ko",
				"Duplicate file error.": "Erreur: Fichier déjà sélectionné.",
				"File size error.": "Erreur de taille de fichier.",
				"N/A": "Non applicable",
				"gb": "Go",
				"Error: Invalid file extension:": "Erreur: Extension de fichier non valide:",
				"Select files": "Sélectionnez les fichiers",
				"%s already present in the queue.": "%s déjà présent dans la file d'attente.",
				"File: %s": "Fichier: %s",
				"b": "o",
				"Uploaded %d/%d files": "%d fichiers sur %d ont été envoyés",
				"Upload element accepts only %d file(s) at a time. Extra files were stripped.": "Que %d fichier(s) peuvent être envoyé(s) à la fois. Les fichiers supplémentaires ont été ignorés.",
				"%d files queued": "%d fichiers en attente",
				"File: %s, size: %d, max file size: %d": "Fichier: %s, taille: %d, taille max. d'un fichier: %d",
				"Drag files here.": "Déposez les fichiers ici.",
				"Runtime ran out of available memory.": "Le traitement a manqué de mémoire disponible.",
				"File count error.": "Erreur: Nombre de fichiers.",
				"File extension error.": "Erreur d'extension de fichier",
				"Error: File too large:": "Erreur: Fichier trop volumineux:",
				"Add Files": "Ajouter"
			});
		}
	}

	$(document).ready(function() {
		bindFancy();
		//custom select box
		$('#dataTicketStatus').multipleSelect({
			single: true,
			filter: false,
			onClick: function(view) {
				view.instance.setSelects([view.value]);
			}
		});
		$('#reset-status').click(function() {
			$('#dataTicketStatus').multipleSelect('setSelects', [$('#TicketOldStatusId').val()]);
		});
		$('#update-button').click(function() {
			$('#TicketUpdateInfoForm').submit();
		});

		// fixed
		//$('#ticket-fields').follow();

		var listAttachments = {
			image: {},
			'document': {}
		};

		$("#queue").pluploadQueue({
			runtimes: 'html5,flash,html4',
			url: '/tickets/upload/' + id,
			dragdrop: true,
			filters: {
				max_file_size: '1000mb',
				mime_types: [{
					title: "Files",
					extensions: <?php echo json_encode($allowedFiles) ?>
				}]
			},
			flash_swf_url: '/js/moxie.swf',
			multiple_queues: true
		});
		var uploader = $('#queue').pluploadQueue();
		var modal = $('#upload-dialog').dialog({
			autoOpen: false,
			width: 'auto',
			modal: true,
			open: function(e, ui) {
				uploader.refresh();
				$('#queue input').css('z-index', '99999');
				listAttachments = {
					image: {},
					'document': {}
				};
			}
		});
		uploader.bind('FileUploaded', function(inst, fl, res) {
			var f = $.parseJSON(res.response);
			if (f) {
				if (f.thumbnail) {
					listAttachments.image[f.id] = f;
				} else {
					listAttachments['document'][f.id] = f;
				}
			}
		});
		uploader.unbind('UploadComplete');
		uploader.bind('UploadComplete', function(inst) {
			modal.dialog('close');
			console.log(listAttachments);

			buildAttachments(listAttachments, true);
			listAttachments = {
				image: {},
				'document': {}
			};
			uploader.refresh();
		});

		buildAttachments();

		var saving;

		//comment form
		$('#comment-form').ajaxForm({
			dataType: 'json',
			beforeSubmit: function(params) {
				if (saving) return false;
				saving = true;
				setLoadding('comment-text', true);
				var content = $.trim(tinyMCE.get('comment-text').getContent());
				if (!content) {
					alert(<?php echo json_encode(__('Cannot be empty', true)) ?>);
					setLoadding('comment-text', false);
					saving = false;
					return false;
				}
				params.push({
					name: 'data[content]',
					value: content
				});
			},
			success: function(comment) {
				if (comment) {
					var y = buildComment(comment, true);
					//scroll
					$('html, body').animate({
						scrollTop: y.offset().top - $('#ticket-fields').height()
					}, 700);
				}
			},
			complete: function() {
				tinyMCE.get('comment-text').setContent('');
				saving = false;
				setLoadding('comment-text', false);
			}
		});

		//submit content
		$('#submit-content').click(function() {
			if (saving) return false;
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
				success: function() {
					//hide editor
					toggleEditor(false);
					editor.setContent('');
					editor.save();
					$('#ticket-content').html(content);
					bindFancy();
				},
				error: function() {
					alert(<?php echo json_encode(__('Problem while saving', true)) ?>);
				},
				complete: function() {
					saving = false;
					setLoadding('ticket-editor', false);
				}
			})
		});
		$('#cancel-content').click(function() {
			if (saving) return false;
			var editor = tinyMCE.get('ticket-editor');
			if (editor.isDirty()) {
				if (!confirm(<?php echo json_encode(__('Discard changes?', true)) ?>)) {
					return false;
				}
			}
			editor.setContent('');
			editor.save();
			toggleEditor(false);
		});

		$('#ticket-info').dblclick(function() {
			if (can_update) {
				toggleEditor(true);
			}
		});

		buildComments();
	});

	function bindFancy() {
		$('.mce-content-body img').off('click').on('click', function() {
			var src = $(this).prop('src');
			$.fancybox({
				'padding': 0,
				'href': src,
				'transitionIn': 'elastic',
				'transitionOut': 'elastic'
			});
		});
	}

	function toggleEditor(t) {
		var ed = tinymce.editors['ticket-editor'];
		if (t) {
			$('#ticket-content').hide();
			ed.show();
			$('.ticket-btn').show();
		} else {
			ed.hide();
			$('#ticket-content').show();
			$('.ticket-btn').hide();
		}
	}


	function toggleCommentEditor(selector, open) {
		var editor = tinymce.get(selector),
			btn = $('#' + selector).siblings('.comment-buttons'),
			div = $('#' + selector).siblings('.comment-text');
		if (open) {
			div.hide();
			editor.show();
			btn.show();
		} else {
			editor.hide();
			btn.hide()
			div.show();
		}
	}

	function bindEditor(selector) {
		tinymce.init({
			selector: '#' + selector,
			autoresize_min_height: 200,
			autoresize_bottom_margin: 0,
			plugins: [
				'advlist autolink lists link image charmap anchor fullscreen table contextmenu textcolor colorpicker emoticons imagetools spellchecker fullscreen paste autoresize painter'
			],
			menubar: false,
			image_advtab: true,
			// statusbar: false,
			toolbar: 'bold italic blockquote forecolor styleselect | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist | link image painter emoticons | fullscreen',
			content_css: [
				'/css/editor.css'
			],
			skin: 'z0',
			language: Azuree.language,

			// use absolute url for image
			relative_urls: false,
			remove_script_host: true,
			convert_urls: true,

			entities: '160,nbsp,162,cent,8364,euro,163,pound',
			entity_encoding: 'raw',
			image_caption: true,

			setup: function(editor) {
				editor.on('init', function(e) {
					e.target.hide();
				});
				editor.on('show', function(e) {
					e.target.setContent($('#content-' + e.target.id).html());
					e.target.save();
				});
			},

			paste_data_images: true,
			automatic_uploads: true,
			images_upload_url: '/user_files/ticket_image_upload/' + id,

			file_browser_callback: function(field_name, url, type, win) {
				if (type == 'image') {
					$('#temp-upload-form input').data({
						name: field_name,
						win: win
					}).click();
				}
			}
		});
	}

	function buildComments(addNew = false) {
		$.each(comments, function(i, n) {
			var cmt = n['TicketComment'];
			buildComment(cmt, addNew);
		});
	}

	function buildComment(comment, addNew = false) {
		var template = $('#comment-template').html();
		comment.name = resources[comment.employee_id];
		if (!comment.time) {
			var a = comment.created.split(/[^\d]+/g),
				time = new Date(a[0], a[1] - 1, a[2], a[3], a[4], 0, 0);
			comment.time = time.getHours() + ':' + time.getMinutes() + ', ' + $.datepicker.formatDate('dd-mm-yy', time);
		}
		// system auto comment
		if (comment.type == 1) {
			var c = $.parseJSON(comment.content);
			comment.content = replace(c, <?php echo json_encode(__('"{old}" to "{new}"', true)) ?>);
		}
		var n = $(replace(comment, template));
		// n.append('#content-comments');
		if (addNew) {
			// $( "#content-comments .comment" ).insertBefore(n);
			if (!$("#content-comments .comment").length) $("#content-comments").append($('<li class="comment border--bottom-none">'));
			n.insertBefore("#content-comments .comment:first");
			// $( "#content-comments" ).insertBefore(n);
		} else $("#content-comments").append(n);
		if (comment.type == 1) {
			n.addClass('comment-system');
			n.find('.comment-text').removeClass('mce-content-body');
			n.find('.edit').closest('ul').remove();
		} else if (comment.employee_id == me) {

			bindEditor('editor-' + comment.id);

			// open editor
			n.off('dblclick').on('dblclick', function() {
				toggleCommentEditor('editor-' + comment.id, true);
			});

			// open editor
			n.find('.edit').off('click').on('click', function() {
				toggleCommentEditor('editor-' + comment.id, true);
				return false;
			});

			// cancel
			n.find('.cancel').off('click').on('click', function() {
				var editor = tinymce.get('editor-' + comment.id);
				if (editor.isDirty() && !confirm(<?php echo json_encode(__('Cancel?', true)) ?>)) {
					return false;
				}
				editor.setContent('');
				editor.save();
				toggleCommentEditor('editor-' + comment.id, false);
			});

			// save
			n.find('.save').off('click').on('click', function() {
				// ajax call
				var editor = tinymce.get('editor-' + comment.id);
				var content = editor.getContent();
				setLoadding('editor-' + comment.id, true);
				$.ajax({
					url: '/ticket_comments/update',
					type: 'POST',
					data: {
						data: {
							id: comment.id,
							ticket_id: id,
							content: content
						}
					},
					success: function() {
						n.find('.comment-text').html(content);
					},
					complete: function() {
						setLoadding('editor-' + comment.id, false);
						editor.save();
						toggleCommentEditor('editor-' + comment.id, false);
					}
				});
			});

			// delete
			n.find('.delete').off('click').on('click', function() {
				// ajax call delete
				if (confirm(<?php echo json_encode(__('Delete?', true)) ?>)) {
					$.ajax({
						url: '/ticket_comments/delete',
						type: 'POST',
						data: {
							data: {
								id: comment.id,
								ticket_id: id
							}
						},
						success: function() {
							n.fadeOut(function() {
								n.remove();
							});
						}
					});
				}
				return false;
			});
		} else {
			n.find('.edit,.delete').parent().remove();
		}

		// reply
		n.find('.reply').off('click').on('click', function() {
			var text = '<a href="#comment-' + comment.id + '"><b>@' + $.trim(n.find('.comment-author:first').text()) + '</b></a>&nbsp;';
			tinymce.get('comment-text').execCommand('mceInsertContent', false, text);
			// scroll
			var y = $('.comment-text-wrapper').offset().top;
			$('html, body').animate({
				scrollTop: y - $('#ticket-fields').height()
			}, 700);
			return false;
		});

		// quote
		n.find('.quote').off('click').on('click', function() {
			var text = '<p></p><blockquote><p><a href="#comment-' + comment.id + '"><b>@' + $.trim(n.find('.comment-author:first').text()) + '</b></a> <i style="color: #999;">(' + comment.time + ')</i></p>';
			text += comment.content;
			text += '</blockquote><p></p>';
			tinymce.get('comment-text').execCommand('mceInsertContent', false, text);
			// scroll
			var y = $('.comment-text-wrapper').offset().top;
			$('html, body').animate({
				scrollTop: y - $('#ticket-fields').height()
			}, 700);
			return false;
		});
		bindFancy();
		return n;
	}

	function bindTip() {
		$('.attachment span').qtip('destroy', true);
		$('.attachment span').qtip({
			show: 'click',
			hide: 'unfocus',
			content: {
				text: function(e, api) {
					var me = $(e.target).closest('.attachment'),
						attachment_id = me.data('id'),
						type = me.hasClass('image') ? 'image' : (me.hasClass('document') ? 'document' : 'link');
					var content = $($('#tooltip-template').html());
					content.find('b').text(me.data('name'));
					content.find('i').text(me.data('size'));
					content.find('.download').on('click', function() {
						window.open('/ticket_attachments/download/' + id + '/' + attachment_id, '_blank');
						$('.attachment span').qtip('hide');
					});
					content.find('.delete').on('click', function() {
						if (confirm(<?php echo json_encode(__('Delete?', true)) ?>)) {
							$('.attachment-' + attachment_id).css('opacity', 0.5);
							$.ajax({
								url: '/ticket_attachments/delete/' + id + '/' + attachment_id,
								success: function(r) {
									if (r) {
										$('.attachment-' + attachment_id).fadeOut(function() {
											$(this).remove();
										});
									}
								}
							});
						}
						$('.attachment span').qtip('hide');
						//location.href = '/ticket_attachments/delete/' + id + '/' + attachment_id;
					});
					console.log(type);
					if (type == 'image') {
						content.find('.insert').on('click', function() {
							if ($('.ticket-btn').is(':visible')) {
								var editor = tinymce.get('ticket-editor'),
									url = <?php echo $this->UserFile->ticketImage('company_id', 'id', 'me.data(\'name\')') ?>;
								editor.execCommand('mceInsertContent', false, '<img src="' + url + '" alt="">');
							}
							$('.attachment span').qtip('hide');
						});
					} else if (type == 'link') {
						content.find('.insert').remove();
						content.find('.download').remove();
					} else {
						content.find('.insert').remove();
					}
					if (!can_update) {
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

	function buildAttachments(a, prepend) {
		if (!a) a = attachments;
		//build image
		if (a.image) {
			var html = '';
			$.each(a.image, function(aid, f) {
				html += prepareAttachment(f);
			});
			if (!prepend) {
				$('#attachment-images').append(html);
			} else {
				$('#attachment-images').prepend(html);
			}
		}
		//build doc
		if (a['document']) {
			var html = '';
			$.each(a['document'], function(aid, f) {
				html += prepareAttachment(f);
			});
			if (!prepend) {
				$('#attachment-documents').append(html);
			} else {
				$('#attachment-documents').prepend(html);
			}
		}
		//build link
		if (a['link']) {
			var html = '';
			$.each(a['link'], function(aid, f) {
				html += prepareAttachment(f);
			});
			if (!prepend) {
				$('#attachment-link').append(html);
			} else {
				$('#attachment-link').prepend(html);
			}
		}

		bindTip();
	}

	function openAttachmentDialog() {
		// if( !canModify && !readOnly )return false;
		$("#template_upload").addClass('show');
		$('.light-popup').addClass('show');

	}

	$('.close, .cancel').on('click', function(e) {
		$("#template_upload").removeClass('show');
		$('.light-popup').removeClass('show');
	});

	$("#ok_attach").on('click', function() {
		var form = $("#UploadViewForm");
		form.ajaxSubmit({
			dataType: 'json',
			success: function(data) {
				form.find('#newDocURL').val('');
				listAttachments = {
					image: {},
					'document': {},
					'link': {}
				};
				if (data) {
					if (data.type == 'image') {
						listAttachments.image[data.id] = data;
					} else if (data.type == 'document') {
						listAttachments['document'][data.id] = data;
					} else {
						listAttachments['link'][data.id] = data;
					}
				}
				buildAttachments(listAttachments, true);
			}
		});
	});
	$(function() {

		var myDropzone = new Dropzone("#upload-popup", {
			acceptedFiles: "",
		});

		myDropzone.on("queuecomplete", function(file) {

		});
		myDropzone.on("success", function(file) {
			myDropzone.removeFile(file);
			listAttachments = {
				image: {},
				'document': {},
				'link': {}
			};
			data = JSON.parse(file.xhr.responseText);
			if (data) {
				if (data.type == 'image') {
					listAttachments.image[data.id] = data;
				} else if (data.type == 'document') {
					listAttachments['document'][data.id] = data;
				} else {
					listAttachments['link'][data.id] = data;
				}
			}
			buildAttachments(listAttachments, true);

		});
		$('#UploadIndexForm').on('submit', function(e) {
			$('#UploadIndexForm').parent('.wd-popup').addClass('loading');
			// return;
			if (myDropzone.files.length) {
				e.preventDefault();
				myDropzone.processQueue();
			}
		});

		myDropzone.on('sending', function(file, xhr, formData) {
			// Append all form inputs to the formData Dropzone will POST
			var data = $('#UploadIndexForm').serializeArray();
			$.each(data, function(key, el) {
				formData.append(el.name, el.value);
			});
		});

	});

	function prepareAttachment(f) {
		var html = '';
		f.name = f.file;
		f.size = fsize(f.size);
		if (f.type == 'image') {
			var content = templates.image;
			f.file = <?php echo $this->UserFile->ticketImage('company_id', 'id', 'f.name') ?>;
			var thumbnail = <?php echo $this->UserFile->ticketImage('company_id', 'id', 'f.thumbnail') ?>;
			f.thumbnail = '<img src="' + thumbnail + '" alt="">';
			html = replace(f, content);
		} else if (f.type == 'document') {
			var content = templates.doc;
			html = replace(f, content);
		} else {
			var content = templates.link;
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

	function replace(obj, str) {
		$.each(obj, function(key, value) {
			str = replaceAll('{' + key + '}', value, str);
		});
		return str;
	}

	function fsize(size) {
		size = parseInt(size);
		var i = Math.floor(Math.log(size) / Math.log(1024));
		return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
	}

	function setLoadding(id, status, inline) {
		var e = $(tinymce.get(id).getContainer());
		if (inline) {
			e = $('#' + id);
		}
		var w = e.width(),
			h = e.height();
		var overlay = $('#to-' + id);
		if (!overlay.length) {
			overlay = $('<div id="to-' + id + '" class="overlay"></div>').appendTo('body');
		}
		overlay.width(w)
			.height(h)
			.css({
				top: e.offset().top,
				left: e.offset().left
			});
		if (status) {
			overlay.show();
		} else {
			overlay.hide();
		}
	}

	function keepLogin() {
		setInterval(function() {
			$.get('/tickets/keepLogin', function(e) {
				if (e == 'true') {
					var _time = new Date();
					console.log('Keep login: ' + _time.toLocaleString());
				} else console.log('Logged out');
			});
		}, 1200000);
	}
</script>