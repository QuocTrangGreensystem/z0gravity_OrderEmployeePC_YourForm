<div class="projectRisks form">
<?php echo $this->Form->create('ProjectRisk');?>
	<fieldset>
		<legend><?php __('Add Project Risk'); ?></legend>
	<?php
		echo $this->Form->input('project_risk');
		echo $this->Form->input('project_id');
		echo $this->Form->input('project_risk_severity_id');
		echo $this->Form->input('project_risk_occurrence_id');
		echo $this->Form->input('risk_assign_to');
		echo $this->Form->input('risk_close_date');
		echo $this->Form->input('actions_manage_risk');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Risks', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risk Severities', true), array('controller' => 'project_risk_severities', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk Severity', true), array('controller' => 'project_risk_severities', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risk Occurrences', true), array('controller' => 'project_risk_occurrences', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk Occurrence', true), array('controller' => 'project_risk_occurrences', 'action' => 'add')); ?> </li>
	</ul>
</div>