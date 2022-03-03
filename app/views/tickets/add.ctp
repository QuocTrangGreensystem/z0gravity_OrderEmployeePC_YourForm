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
	'jquery.fancybox'
));
//format
App::import('vendor', 'str_utility');
?>

<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-title">
					<!-- buttons list -->
					<button class="btn-text btn-green" style="margin-top: 5px" id="update-button">
						<img src="/img/ui/blank-save.png" alt="">
						<span><?php __('Save') ?></span>
					</button>
				</div>
				<?php echo $this->Form->create('Ticket', array('action' => 'update_info')) ?>
				<?php echo $this->Validation->bind('Ticket'); ?>
				<div id="the-ticket">
					<table id="ticket-fields" class="display">
						<tr>
							<th><?php __('ID') ?></th>
							<th><?php __('Descriptions') ?></th>
							<th><?php __('Type') ?></th>
							<th><?php __('Priority') ?></th>
							<?php if ($profile['role'] != 'customer') : ?>
								<th><?php __('Subscribe') ?></th>
							<?php endif ?>
							<th><?php __('Company') ?></th>
						</tr>
						<tr>
							<td>#</td>
							<td>
								<?php echo $this->Form->hidden('id') ?>
								<?php echo $this->Form->input('name', array(
									'label' => false
								)) ?>
							</td>
							<td>
								<?php echo $this->Form->input('type_id', array(
									'type' => 'select',
									'options' => !empty($metas['type']) ? $metas['type'] : array(),
									'label' => false,
									'empty' => __('-- Select -- ', true),
									'class' => 'popup__external--incidentguide'
								)) ?>
							</td>
							<td>
								<?php echo $this->Form->input('priority_id', array(
									'type' => 'select',
									'options' => !empty($metas['priority']) ? $metas['priority'] : array(),
									'label' => false,
									'empty' => __('-- Select -- ', true)
								)) ?>
							</td>
							<?php if ($profile['role'] != 'customer') : ?>
								<td>
									<?php echo $this->Form->input('subscribe', array(
										'type' => 'checkbox',
										'value' => '1',
										'label' => true,
									)) ?>
								</td>
							<?php endif ?>
							<td>
								<?php if ($profile['role'] == 'customer') : ?>
									<?php echo $this->Form->input('subscribe', array(
										'type' => 'checkbox',
										'class' => 'hidden',
										'value' => '1',
										'label' => false
									)) ?>
								<?php endif ?>
								<?php if (!empty($profile['role']) && ($profile['role'] == 'developer')) : ?>
									<div class="input select">
										<select name="data[Ticket][company_id]" id="company-select">
											<option value="Company-<?php echo $company_id ?>" <?php if (!$is_external) echo 'selected' ?>><?php echo $company_name ?></option>
											<?php foreach ($external_companies as $id => $name) { ?>
												<option value="External-<?php echo $id ?>" <?php if ($is_external && $cid == $id) echo 'selected' ?>><?php echo $name ?> (<?php __('External') ?>)</option>
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
									<?php echo $is_external && !empty($external_companies[$cid]) ? $external_companies[$cid] : (!empty($company_name) ? $company_name : '') ?>
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
							<td id="references">
							</td>
							<td>
								<div class="input select">
									<select name="data[Ticket][ticket_status_id]" id="dataTicketStatus">
										<?php foreach ($visible_statuses as $id => $status) { ?>
											<option value="<?php echo $id ?>" <?php if ($status['is_default']) echo 'selected' ?>><?php echo $status['name'] ?></option>
										<?php } ?>
									</select>

								</div>
							</td>
							<?php if ($profile['role'] != 'customer') : ?>
								<td>
									<?php if (!empty($profile['role']) && ($profile['role'] == 'developer')) : ?>
										<?php echo $this->Form->input('delivery_date', array(
											'label' => false,
											'type' => 'text',
											'class' => 'datepicker'
										)) ?>
									<?php endif ?>
								</td>
							<?php endif ?>
							<td>
								<?php echo $this->Form->input('function_id', array(
									'type' => 'select',
									'options' => !empty($metas['function']) ? $metas['function'] : array(),
									'label' => false,
									'empty' => __('-- Select -- ', true)
								)) ?>
							</td>
							<td>
								<?php echo $this->Form->input('version_id', array(
									'type' => 'select',
									'options' => !empty($metas['version']) ? $metas['version'] : array(),
									'label' => false,
									'empty' => __('-- Select -- ', true)
								)) ?>
							</td>
							<td>
								<div style="text-align: left;min-width: 180px; margin-left: 10px;">
									<img src="<?php echo $this->UserFile->avatar($me, 'large') ?>" class="ticket-avatar" style="margin-right: 10px" alt="">
									<div class="avatar-top">
										<b><?php echo $employee_info['Employee']['fullname'] ?></b>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<?php echo $this->Form->hidden('redirect', array('value' => '/tickets/view/{id}/ticket')) ?>
				<?php echo $this->Form->end() ?>
			</div>
		</div>
	</div>
</div>
<style>
	.wd-dialog-2019 img {
		width: 100%;
		margin: 20px 0;
	}

	#wdConfimDialog {
		overflow: auto;
	}
</style>
<?php echo $this->element("dialog_detail_value") ?>
<div id="popup__external--incidentguide" class="hidden">
	<div class="dialog-content auto">
		<h2>Bonjour,</h2>
		<br>
		<p>
			Pour que votre ticket puisse t'être traité, nous vous remercions de suivre les intructions suivantes
		</p>
		<br>
		<p>
			1) Avant d'ouvrir un ticket d'incident, merci de vérifier que votre navigateur est à jour.
		</p>
		<br>
		<p>
			2) Pour qu'un ticket soit pris en compte par l'équipe support, vous devez faire une copie d'écran en mode privé.
		</p>


		<img src="/img/ticket/popup-ticket-incident-guide1.png" />

		<p>
			3) La capture d'écran doit afficher l'URL (1) , le nom de la personne connectée (2) , l'affichage en mode privée. (3)
		</p>

		<img src="/img/ticket/popup-ticket-incident-guide2.png" />

		<p>
			Si besoin, merci de mettre plusieurs captures décran.
		</p>
		<p>
			Si votre ticket n'est pas un ticket incident il sera requalifié en ticket de support fonctionnel.
		</p>
		<br>
		<p>
			Nous vous remercions.
		</p>
		<p>
			L'équipe z0 Gravity.
		</p>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		$('.datepicker').datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$('#update-button').click(function() {
			$('#TicketUpdateInfoForm').submit();
		});
		if ($(".popup__external--incidentguide option:selected").text() == 'INCIDENT/ANOMALIE') {
			alert('popup__external--incidentguide' + $(".popup__external--incidentguide option:selected").text());
		}
		let types = <?php echo json_encode(@$metas['type']); ?>;
		let is_external = <?php echo $is_external; ?>;
		$(".popup__external--incidentguide").on('change', function(e) {
			let _this = $(this);
			let val = _this.val();
			// if((val in $types) && $types[val] == 'INCIDENT/ANOMALIE') showMe();
			if ((val in types) && types[val] == 'INCIDENT/ANOMALIE' && is_external == 1) wdConfirmIt({
				title: '',
				content: $('#popup__external--incidentguide').html(),
				buttonModel: 'WD_ONE_BUTTON',
				buttonText: ["<?php echo __('Close', true); ?>"],
				width: parseInt($(window).width() * 0.7),
				height: Math.min(700, parseInt($(window).height() * 0.7))
			});
		});
	});
</script>