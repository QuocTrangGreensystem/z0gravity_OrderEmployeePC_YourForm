<div class="projectStatuses form">
<?php echo $this->Form->create('ProjectStatus');?>
	<fieldset>
		<legend><?php __('Edit Project Status'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectStatus.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectStatus.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Statuses', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>