<div class="projectPhasePlans form">
<?php echo $this->Form->create('ProjectPhasePlan');?>
	<fieldset>
		<legend><?php __('Add Project Phase Plan'); ?></legend>
	<?php
		echo $this->Form->input('project_id');
		echo $this->Form->input('project_planed_phase_id');
		echo $this->Form->input('project_phase_status_id');
		echo $this->Form->input('phase_planed_start_date');
		echo $this->Form->input('phase_planed_end_date');
		echo $this->Form->input('phase_real_start_date');
		echo $this->Form->input('phase_real_end_date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Phase Plans', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phases', true), array('controller' => 'project_phases', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase', true), array('controller' => 'project_phases', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Statuses', true), array('controller' => 'project_phase_statuses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Status', true), array('controller' => 'project_phase_statuses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Statuses', true), array('controller' => 'project_statuses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Status', true), array('controller' => 'project_statuses', 'action' => 'add')); ?> </li>
	</ul>
</div>