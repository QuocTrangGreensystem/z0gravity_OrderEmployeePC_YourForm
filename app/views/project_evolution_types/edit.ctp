<div class="projectEvolutionTypes form">
<?php echo $this->Form->create('ProjectEvolutionType');?>
	<fieldset>
		<legend><?php __('Edit Project Evolution Type'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('project_type_evolution');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectEvolutionType.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectEvolutionType.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Evolution Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Evolutions', true), array('controller' => 'project_evolutions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution', true), array('controller' => 'project_evolutions', 'action' => 'add')); ?> </li>
	</ul>
</div>