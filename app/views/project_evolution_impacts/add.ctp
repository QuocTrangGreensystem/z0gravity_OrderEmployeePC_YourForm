<div class="projectEvolutionImpacts form">
<?php echo $this->Form->create('ProjectEvolutionImpact');?>
	<fieldset>
		<legend><?php __('Add Project Evolution Impact'); ?></legend>
	<?php
		echo $this->Form->input('evolution_impact');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Evolution Impacts', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Evolution Impact Refers', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution Impact Refer', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'add')); ?> </li>
	</ul>
</div>