<div class="projectFunctions form">
<?php echo $this->Form->create('ProjectFunction');?>
	<fieldset>
		<legend><?php __('Edit Project Function'); ?></legend>
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

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectFunction.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectFunction.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Functions', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Teams', true), array('controller' => 'project_teams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Team', true), array('controller' => 'project_teams', 'action' => 'add')); ?> </li>
	</ul>
</div>