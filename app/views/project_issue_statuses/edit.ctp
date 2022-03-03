<div class="projectIssueStatuses form">
<?php echo $this->Form->create('ProjectIssueStatus');?>
	<fieldset>
		<legend><?php __('Edit Project Issue Status'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('issue_status');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectIssueStatus.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectIssueStatus.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Issue Statuses', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Issues', true), array('controller' => 'project_issues', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue', true), array('controller' => 'project_issues', 'action' => 'add')); ?> </li>
	</ul>
</div>