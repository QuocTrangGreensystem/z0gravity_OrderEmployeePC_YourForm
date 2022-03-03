<div class="projectEvolutionTypes form">
<?php echo $this->Form->create('ProjectEvolutionType');?>
	<fieldset>
		<legend><?php __('Add Project Evolution Type'); ?></legend>
	<?php
		echo $this->Form->input('project_type_evolution');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Evolution Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Evolutions', true), array('controller' => 'project_evolutions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution', true), array('controller' => 'project_evolutions', 'action' => 'add')); ?> </li>
	</ul>
</div>