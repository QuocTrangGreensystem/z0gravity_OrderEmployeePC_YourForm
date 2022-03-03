<div class="projectIssueSeverities form">
<?php echo $this->Form->create('ProjectIssueSeverity');?>
	<fieldset>
		<legend><?php __('Add Project Issue Severity'); ?></legend>
	<?php
		echo $this->Form->input('issue_severity');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Issue Severities', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Issues', true), array('controller' => 'project_issues', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue', true), array('controller' => 'project_issues', 'action' => 'add')); ?> </li>
	</ul>
</div>