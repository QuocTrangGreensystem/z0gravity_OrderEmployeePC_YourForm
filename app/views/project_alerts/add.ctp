<div class="ProjectAlerts form">
<?php echo $this->Form->create('ProjectAlert');?>
	<fieldset>
		<legend><?php __('Add Project Alert'); ?></legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Alert', true), array('action' => 'index'));?></li>
	</ul>
</div>
