<div class="projectIssues form">
<?php echo $this->Form->create('ProjectIssue');?>
	<fieldset>
		<legend><?php __('Edit Project Issue'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('project_id');
		echo $this->Form->input('project_issue_problem');
		echo $this->Form->input('project_issue_severity_id');
		echo $this->Form->input('project_issue_status_id');
		echo $this->Form->input('issue_assign_to');
		echo $this->Form->input('issue_action_related');
		echo $this->Form->input('date_issue_close');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectIssue.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectIssue.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Issues', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Issue Severities', true), array('controller' => 'project_issue_severities', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue Severity', true), array('controller' => 'project_issue_severities', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Issue Statuses', true), array('controller' => 'project_issue_statuses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue Status', true), array('controller' => 'project_issue_statuses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Employees', true), array('controller' => 'employees', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Employee', true), array('controller' => 'employees', 'action' => 'add')); ?> </li>
	</ul>
</div>