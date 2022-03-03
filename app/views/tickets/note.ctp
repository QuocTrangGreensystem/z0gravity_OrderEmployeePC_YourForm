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
	'editor',
));
//format
App::import('vendor', 'str_utility');
$ticket['delivery_date'] = $ticket['delivery_date'] ? str_utility::convertToVNDate($ticket['delivery_date']) : '';
?>

<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
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
				</div>
				<?php echo $this->Form->hidden('redirect', array('value' => '/tickets/view/' . $ticket_id . '/' . $screen)) ?>
				<?php echo $this->Form->end() ?>
				<div id="ticket-notes" class="ticket-section">
					<h3 class="ticket-header"><?php __('Notes') ?></h3>
					<ul id="comment-list">
						
						<!-- form -->
						<?php echo $this->Form->create(false, array('url' => '/ticket_notes/update', 'id' => 'comment-form')) ?>
						<li class="comment comment-form">
							<img src="<?php echo $this->UserFile->avatar($me, 'large') ?>" alt="" class="ticket-avatar comment-avatar my-avatar">
							<div class="comment-text-wrapper">
								<textarea id="comment-text" cols="30" rows="10"></textarea>
								<button type="submit" class="btn-text" style="margin-top: 5px">
									<img src="/img/ui/blank-save.png" alt="">
									<span><?php __('Save') ?></span>
								</button>
							</div>
							<?php echo $this->Form->hidden('content') ?>
							<?php echo $this->Form->hidden('ticket_id', array('value' => $ticket_id)) ?>
							<?php echo $this->Form->hidden('employee_id', array('value' => $me)) ?>
						</li>
						<?php echo $this->Form->end() ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<ul id="comment-template" style="display: none">
	<li class="comment comment-{id}" data-id="{id}">
		<img src="<?php echo $this->UserFile->avatar('{employee_id}', 'large') ?>" alt="" class="ticket-avatar comment-avatar">
		<ul class="comment-actions">
			<li><a class="edit edit-{id}" href="#"><span>link</span></a></li>
			<li><a class="delete delete-{id}" href="#"><span>link</span></a></li>
		</ul>
		<div class="comment-content">
			<h3 class="comment-author">
				{name}
			</h3>
			<i class="comment-time">
				{time}
			</i>
			<div class="comment-text mce-content-body" id="content-editor-{id}">{content}</div>
			<textarea id="editor-{id}" style="display: none"></textarea>
			<div class="comment-buttons">
				<button type="button" class="btn-text save">
					<img src="/img/ui/blank-save.png" alt="">
					<span><?php __('Save') ?></span>
				</button>
				<button type="button" class="btn-text btn-red cancel" data-id="{id}">
					<img src="/img/ui/blank-reset.png" alt="">
					<span><?php __('Cancel') ?></span>
				</button>
			</div>
		</div>
	</li>
</ul>

<div id="edit-template" style="display: none">
</div>
<form method="post" action="/user_files/ticket_image_upload/<?php echo $ticket_id ?>" style="width: 0; height: 0; overflow: hidden" enctype="multipart/form-data" id="temp-upload-form">
	<input name="image" type="file" onchange="doUpload.call(this)">
</form>
<script>
var id = <?php echo $ticket_id ?>,
	company_id = <?php echo $company_id ?>,
	resources = <?php echo json_encode($resources) ?>,
	can_update = <?php echo json_encode($can_update) ?>,
	notes = <?php echo json_encode($notes) ?>;

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
	relative_urls : false,
	remove_script_host : true,
	convert_urls : true,

	entities : '160,nbsp,162,cent,8364,euro,163,pound',
	entity_encoding: 'raw',
	image_caption: true,

	paste_data_images: true,
	automatic_uploads: true,
	images_upload_url: '/user_files/ticket_image_upload/' + id,

	file_browser_callback: function(field_name, url, type, win) {
        if(type == 'image'){
        	$('#temp-upload-form input').data({
        		name: field_name,
        		win: win
        	}).click();
        }
    }
});
function bindFancy(){
	$('.mce-content-body img').off('click').on('click', function(){
		var src = $(this).prop('src');
		$.fancybox({
			'padding'		: 0,
			'href'			: src,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	});
}

$(document).ready(function(){
	// fixed
	bindFancy();

	var saving;
	buildNotes();


	$('#comment-form').ajaxForm({
		dataType: 'json',
		beforeSubmit: function(params){
			if( saving )return false;
			saving = true;
			setLoadding('comment-text', true);
			var content = $.trim(tinyMCE.get('comment-text').getContent());
			if( !content ){
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
		success: function(note){
			if( note ){
				buildNote(note);
			}
		},
		complete: function(){
			tinyMCE.get('comment-text').setContent('');
			saving = false;
			setLoadding('comment-text', false);
		}
	});
	if( !can_update ){
		$('#comment-form').remove();
	}
});

function doUpload(){
	var me = $(this);
	$('#temp-upload-form').ajaxSubmit({
		dataType: 'json',
		success: function(d){
			var url = d.location,
				win = me.data('win');
			win.document.getElementById(me.data('name')).value = url;
		}
	});
	this.value = '';
}

function bindEditor(selector){
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
		relative_urls : false,
		remove_script_host : true,
		convert_urls : true,

		entities : '160,nbsp,162,cent,8364,euro,163,pound',
		entity_encoding: 'raw',
		image_caption: true,

		paste_data_images: true,
		automatic_uploads: true,
		images_upload_url: '/user_files/ticket_image_upload/' + id,

		setup: function(editor){
			editor.on('init', function(e){
				e.target.hide();
			});
			editor.on('show', function(e){
				e.target.setContent($('#content-' + e.target.id).html());
				e.target.save();
			});
		},

		file_browser_callback: function(field_name, url, type, win) {
	        if(type == 'image'){
	        	$('#temp-upload-form input').data({
	        		name: field_name,
	        		win: win
	        	}).click();
	        }
	    }
	});
}

function toggleEditor(selector, open){
	var editor = tinymce.get(selector),
		btn = $('#' + selector).siblings('.comment-buttons'),
		div = $('#' + selector).siblings('.comment-text');
	if( open ){
		div.hide();
		editor.show();
		btn.show();
	} else {
		editor.hide();
		btn.hide()
		div.show();
	}
}

// function toggleEditor(selector, open){
// 	if( !can_update )return false;
// 	if( open ){
// 		tinymce.get(selector).setMode('design');
// 		$('#' + selector).siblings('.comment-buttons').show();
// 	} else {
// 		tinymce.get(selector).setMode('readonly');
// 		$('#' + selector).siblings('.comment-buttons').hide();
// 	}
// }

function buildNotes(){
	$.each(notes, function(i, n){
		var note = n['TicketNote'];
		buildNote(note);
	});
}

function buildNote(note){
	var template = $('#comment-template').html();
	note.name = resources[note.employee_id];
	if( !note.time ){
		var time = new Date(note.created);
		note.time = time.getHours() + ':' + time.getMinutes() + ', ' + $.datepicker.formatDate('dd-mm-yy', time);
	}
	var n = $(replace(note, template));
	n.insertBefore('.comment-form');
	bindEditor('editor-' + note.id);

	// open editor
	n.off('dblclick').on('dblclick', function(){
		toggleEditor('editor-' + note.id, true);
	});

	// open editor
	n.find('.edit').off('click').on('click', function(){
		toggleEditor('editor-' + note.id, true);
		return false;
	});

	// cancel
	n.find('.cancel').off('click').on('click', function(){
		var editor = tinymce.get('editor-' + note.id);
		if( editor.isDirty() && !confirm(<?php echo json_encode(__('Cancel?', true)) ?>) ){
			return false;
		}
		editor.setContent('');
		editor.save();
		toggleEditor('editor-' + note.id, false);
	});

	// save
	n.find('.save').off('click').on('click', function(){
		// ajax call
		var editor = tinymce.get('editor-' + note.id);
		var content = editor.getContent();
		setLoadding('editor-' + note.id, true);
		$.ajax({
			url: '/ticket_notes/update',
			type: 'POST',
			data: {
				data: {
					id: note.id,
					ticket_id: id,
					content: content
				}
			},
			success: function(){
				n.find('.comment-text').html(content);
			},
			complete: function(){
				setLoadding('editor-' + note.id, false);
				editor.save();
				toggleEditor('editor-' + note.id, false);
			}
		});
	});

	// delete
	n.find('.delete').off('click').on('click', function(){
		if( !can_update )return false;
		// ajax call delete
		if( confirm(<?php echo json_encode(__('Delete?', true)) ?>) ){
			$.ajax({
				url: '/ticket_notes/delete',
				type: 'POST',
				data: {
					data: {
						id: note.id,
						ticket_id: id
					}
				},
				success: function(){
					n.fadeOut(function() {
						n.remove();
					});
				}
			});
		}
		return false;
	});
	bindFancy();
}

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
