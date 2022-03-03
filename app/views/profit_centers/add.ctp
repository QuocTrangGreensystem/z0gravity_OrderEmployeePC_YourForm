<div class="projectFunctions form">
<?php echo $this->Form->create('ProjectFunction');?>
	<fieldset>
		<legend><?php __('Add Project Function'); ?></legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Functions', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Teams', true), array('controller' => 'project_teams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Team', true), array('controller' => 'project_teams', 'action' => 'add')); ?> </li>
	</ul>
</div>