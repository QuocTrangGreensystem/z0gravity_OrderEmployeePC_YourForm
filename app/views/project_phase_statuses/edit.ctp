<div class="projectPhaseStatuses form">
<?php echo $this->Form->create('ProjectPhaseStatus');?>
	<fieldset>
		<legend><?php __('Edit Project Phase Status'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('phase_status');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectPhaseStatus.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectPhaseStatus.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Phase Statuses', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Phase Plans', true), array('controller' => 'project_phase_plans', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Plan', true), array('controller' => 'project_phase_plans', 'action' => 'add')); ?> </li>
	</ul>
</div>