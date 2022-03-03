<div class="projectFunctions view">
<h2><?php  __('Project Function');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectFunction['ProjectFunction']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectFunction['ProjectFunction']['name']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Function', true), array('action' => 'edit', $projectFunction['ProjectFunction']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Function', true), array('action' => 'delete', $projectFunction['ProjectFunction']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectFunction['ProjectFunction']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Functions', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Function', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Teams', true), array('controller' => 'project_teams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Team', true), array('controller' => 'project_teams', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Teams');?></h3>
	<?php if (!empty($projectFunction['ProjectTeam'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th><?php __('Employee Id'); ?></th>
		<th><?php __('Project Function Id'); ?></th>
		<th><?php __('Start Date'); ?></th>
		<th><?php __('End Date'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectFunction['ProjectTeam'] as $projectTeam):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectTeam['id'];?></td>
			<td><?php echo $projectTeam['project_id'];?></td>
			<td><?php echo $projectTeam['employee_id'];?></td>
			<td><?php echo $projectTeam['project_function_id'];?></td>
			<td><?php echo $projectTeam['start_date'];?></td>
			<td><?php echo $projectTeam['end_date'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_teams', 'action' => 'view', $projectTeam['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_teams', 'action' => 'edit', $projectTeam['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_teams', 'action' => 'delete', $projectTeam['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectTeam['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Team', true), array('controller' => 'project_teams', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
