<div class="projectPhases form">
<?php echo $this->Form->create('ProjectPhase');?>
	<fieldset>
		<legend><?php __('Edit Project Phase'); ?></legend>
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

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectPhase.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectPhase.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Phases', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Plans', true), array('controller' => 'project_phase_plans', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Plan', true), array('controller' => 'project_phase_plans', 'action' => 'add')); ?> </li>
	</ul>
</div>