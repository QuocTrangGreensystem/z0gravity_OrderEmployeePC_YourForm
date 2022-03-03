<div class="projectIssueStatuses view">
<h2><?php  __('Project Issue Status');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectIssueStatus['ProjectIssueStatus']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Issue Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectIssueStatus['ProjectIssueStatus']['issue_status']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Issue Status', true), array('action' => 'edit', $projectIssueStatus['ProjectIssueStatus']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Issue Status', true), array('action' => 'delete', $projectIssueStatus['ProjectIssueStatus']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectIssueStatus['ProjectIssueStatus']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Issue Statuses', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue Status', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Issues', true), array('controller' => 'project_issues', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Issue', true), array('controller' => 'project_issues', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Issues');?></h3>
	<?php if (!empty($projectIssueStatus['ProjectIssue'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th><?php __('Project Issue Problem'); ?></th>
		<th><?php __('Project Issue Severity Id'); ?></th>
		<th><?php __('Project Issue Status Id'); ?></th>
		<th><?php __('Issue Assign To'); ?></th>
		<th><?php __('Issue Action Related'); ?></th>
		<th><?php __('Date Issue Close'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectIssueStatus['ProjectIssue'] as $projectIssue):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectIssue['id'];?></td>
			<td><?php echo $projectIssue['project_id'];?></td>
			<td><?php echo $projectIssue['project_issue_problem'];?></td>
			<td><?php echo $projectIssue['project_issue_severity_id'];?></td>
			<td><?php echo $projectIssue['project_issue_status_id'];?></td>
			<td><?php echo $projectIssue['issue_assign_to'];?></td>
			<td><?php echo $projectIssue['issue_action_related'];?></td>
			<td><?php echo $projectIssue['date_issue_close'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_issues', 'action' => 'view', $projectIssue['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_issues', 'action' => 'edit', $projectIssue['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_issues', 'action' => 'delete', $projectIssue['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectIssue['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Issue', true), array('controller' => 'project_issues', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
