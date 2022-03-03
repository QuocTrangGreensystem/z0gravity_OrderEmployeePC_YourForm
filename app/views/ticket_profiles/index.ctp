<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<style>
	.permission ul {
		list-style: square outside;
		margin-left: 20px;
	}
	.permission ul li {
		list-style: inherit;
	}
	.permission-box {
		overflow: hidden;
		clear: both;
		padding-top: 20px;
	}
	.permission-box h3 {
		float: left;
		padding-top: 5px;
		min-width: 100px;
		margin-right: 10px;
		text-align: right;
	}
	.permission-box div {
		float: left;
		overflow: hidden;
	}
	.permission-box label {
		width: auto;
		float: right;
		text-align: left;
		padding: 0 5px;
		margin-right: 10px;
		font-weight: normal;
	}
	.permission-box input {
		float: left;
		margin-top: 8px;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">

		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
								<h2 class="wd-t3"></h2>

								<form action="" id="table-list-admin-form" style="height: 200px; overflow: auto !important;">
									<table cellspacing="0" cellpadding="0" class="display" id="table-list-admin">
										<thead>
											<tr class="wd-header">
												<th class="wd-order" width="5%">#</th>
												<th class="wd-left"><?php __('Name') ?></th>
												<th class="wd-left"><?php __('English') ?></th>
												<th class="wd-left"><?php __('French') ?></th>
												<th class="wd-left"><?php __('Permissions') ?></th>
												<th><?php __("Action") ?></th>
											</tr>
										</thead>
										<tbody>
										<?php
											$i = 1;
											foreach ($data as $ii):
												$item = $ii['TicketProfile'];
												$id = $item['id'];
												$permissionText = array();
												if( $item['role'] == 'developer' ){
													$permissionText[] = __('Full controls', true);
												} else {
													if( !empty($permissions['can_view'][$item['can_view']]) ){
														$permissionText[] = __($permissions['can_view'][$item['can_view']], true);
													}
													if( !empty($permissions['can_create'][$item['can_create']]) ){
														$permissionText[] = __($permissions['can_create'][$item['can_create']], true);
													}
													if( !empty($permissions['can_update'][$item['can_update']]) ){
														$permissionText[] = __($permissions['can_update'][$item['can_update']], true);
													}
												}
												if( empty($permissionText) ){
													$text = '<span style="red">' . __('No access', true) . '</span>';
												} else {
													$text = '<ul><li>' . implode('</li><li>', $permissionText) . '</li></ul>';
												}
										?>
											<tr style="vertical-align: middle">
												<td><?php echo $i++; ?></td>
												<td>
													<span id="name-<?php echo $id ?>"><?php echo $item['name'] ?></span><br/>
													<i style="color: #666" id="role-<?php echo $id ?>" data-role="<?php echo $item['role'] ?>"><?php echo $roles[$item['role']] ?></i>
												</td>
												<td id="eng-<?php echo $id ?>"><?php echo $item['description_eng'] ?></td>
												<td id="fre-<?php echo $id ?>"><?php echo $item['description_fre'] ?></td>
												<td id="permission-<?php echo $id ?>" data-permission="<?php printf('#can_view%s-#can_create%s-#can_update%s', $item['can_view'], $item['can_create'], $item['can_update']) ?>" class="permission">
													<?php echo $text ?>
												</td>
												<td class="wd-action" nowrap >
													<a class="wd-edit" title="<?php __('Edit') ?>" href="javascript:void(0)" onclick="edit(<?php echo $id ?>)" ><?php __('Edit') ?></a>
													<div class="wd-bt-big"><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $id), array('class' => 'wd-hover-advance-tooltip'), __('Delete?', true)) ?>
													</div>       
												</td>
											</tr>
										<?php
											endforeach;
										?>
										</tbody>
									</table>
								</form>

								<div class="wd-add-employee"></div>
								<?php echo $this->Form->create('TicketProfile', array("action" => "update")); ?>
								<?php echo $this->Validation->bind("TicketProfile"); ?>
								<?php echo $this->Session->flash(); ?>
								<fieldset>
									<div class="wd-scroll-form-min">
										<div class="wd-left-content">
											<div class="wd-input ">
												<label for="last-priority"><?php __("Name") ?></label>	
												<?php
												echo $this->Form->input('name', array(
													'type' => 'text',
													'div' => false,
													'label' => false,
													"class" => "placeholder",
													"placeholder" => __('Name', true)
												));
												?>
											</div>
											<div class="wd-input ">
												<label for="last-priority"><?php __("English") ?></label>	
												<?php
												echo $this->Form->input('description_eng', array(
													'type' => 'text',
													'div' => false,
													'label' => false,
													"class" => "placeholder"
												));
												?>
											</div>
										</div>
										<div class="wd-right-content">
											<div class="wd-input ">
												<label for="last-priority"><?php __("Role") ?></label>	
												<?php
												echo $this->Form->input('role', array(
													'type' => 'select',
													'div' => false,
													'label' => false,
													'options' => $roles
												));
												?>
											</div>
											<div class="wd-input ">
												<label for="last-priority"><?php __("French") ?></label>	
												<?php
												echo $this->Form->input('description_fre', array(
													'type' => 'text',
													'div' => false,
													'label' => false,
													"class" => "placeholder"
												));
												?>
											</div>
										</div>
										<div class="permission-box">
											<h3><?php __('Permissions') ?></h3>
											<?php echo $this->Form->hidden('can_view', array('value' => 0)) ?>
											<?php echo $this->Form->hidden('can_create', array('value' => 0)) ?>
											<?php echo $this->Form->hidden('can_update', array('value' => 0)) ?>
											<?php foreach ($permissions as $key => $list) {
												foreach ($list as $value => $text) {
													$text = __($text, true);
											?>
											<?php
												echo $this->Form->input($key, array(
													'type' => 'checkbox',
													'div' => 'xx',
													'label' => $text,
													'value' => $value,
													'hiddenField' => false,
													'id' => $key . $value
												))
											?>
											<?php
												}
											}
											?>
										</div>
										<?php echo $this->Form->hidden('id') ?>
									</div>	
									<div class="wd-submit">
										<button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
											<span><?php __('Save') ?></span>
										</button>
										<a href="javascript:void(0)" class="wd-reset" style="float: none; margin: 0"><?php __('Reset') ?></a>
									</div>
								</fieldset>
								<?php echo $this->Form->end(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function edit(id){
	$('#TicketProfileId').val(id);
	$('#TicketProfileName').val($('#name-' + id).text()).focus();
	var role = $('#role-' + id).data('role');
	$('#TicketProfileRole').val(role);
	$('#TicketProfileDescriptionEng').val($('#eng-' + id).text());
	$('#TicketProfileDescriptionFre').val($('#fre-' + id).text());
	//permissions
	$('#TicketProfileRole').trigger('change');
	var p = $('#permission-' + id).data('permission').replace(/\-/g, ',');
	$('.xx input:checkbox').prop('checked', false);
	$(p).prop('checked', true);
}
function reset(){
	$('#TicketProfileId').val('');
	$('#TicketProfileName').val('').focus();
	$('#TicketProfileDescriptionEng').val('');
	$('#TicketProfileDescriptionFre').val('');
	$('.xx input:checkbox').prop('checked', false);
}
$(document).ready(function(){
	$('.wd-reset').click(function(){
		reset();
	});
	$('.permission li, .xx label').each(function(){
		var me = $(this);
    	me.html( me.text().replace(/(^\w+)/,'<strong>$1</strong>') );
	});
	$('#TicketProfileRole').change(function(){
		var role = $(this).val();
		if( role == 'developer' ){
			$('.permission-box').hide();
		} else {
			$('.permission-box').show();
		}
	});
});
	
</script>