<div class="projectEvolutions form">
<?php echo $this->Form->create('ProjectEvolution');?>
	<fieldset>
		<legend><?php __('Edit Project Evolution'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('project_id');
		echo $this->Form->input('project_evolution');
		echo $this->Form->input('project_evolution_type_id');
		echo $this->Form->input('evolution_applicant');
		echo $this->Form->input('evolution_date_validated');
		echo $this->Form->input('evolution_validator');
		echo $this->Form->input('supplementary_budget');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectEvolution.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectEvolution.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Evolutions', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Evolution Types', true), array('controller' => 'project_evolution_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution Type', true), array('controller' => 'project_evolution_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Evolution Impact Refers', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution Impact Refer', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'add')); ?> </li>
	</ul>
</div>