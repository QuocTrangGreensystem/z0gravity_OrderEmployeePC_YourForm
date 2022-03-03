<div class="projectIssues view">
<h2><?php  __('Project Issue');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectIssue['ProjectIssue']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectIssue['Project']['id'], array('controller' => 'projects', 'action' => 'view', $projectIssue['Project']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Issue Problem'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectIssue['ProjectIssue']['project_issue_problem']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Issue Severity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectIssue['ProjectIssueSeverity']['issue_severity'], array('controller' => 'project_issue_severities', 'action' => 'view', $projectIssue['ProjectIssueSeverity']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Issue Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectIssue['ProjectIssueStatus']['issue_status'], array('controller' => 'project_issue_statuses', 'action' => 'view', $projectIssue['ProjectIssueStatus']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Employee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectIssue['Employee']['id'], array('controller' => 'employees', 'action' => 'view', $projectIssue['Employee']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Issue Action Related'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectIssue['ProjectIssue']['issue_action_related']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Date Issue Close'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectIssue['ProjectIssue']['date_issue_close']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Issue', true), array('action' => 'edit', $projectIssue['ProjectIssue']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Issue', true), array('action' => 'delete', $projectIssue['ProjectIssue']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectIssue['ProjectIssue']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Issues', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue', true), array('action' => 'add')); ?> </li>
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
