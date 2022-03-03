<div class="projectPhases view">
<h2><?php  __('Project Phase');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhase['ProjectPhase']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhase['ProjectPhase']['name']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Phase', true), array('action' => 'edit', $projectPhase['ProjectPhase']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Phase', true), array('action' => 'delete', $projectPhase['ProjectPhase']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectPhase['ProjectPhase']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phases', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Plans', true), array('controller' => 'project_phase_plans', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Plan', true), array('controller' => 'project_phase_plans', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Projects');?></h3>
	<?php if (!empty($projectPhase['Project'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Name'); ?></th>
		<th><?php __('Project Manager Id'); ?></th>
		<th><?php __('Project Phase Id'); ?></th>
		<th><?php __('Project Priority Id'); ?></th>
		<th><?php __('Project Status Id'); ?></th>
		<th><?php __('Start Date'); ?></th>
		<th><?php __('Planed End Date'); ?></th>
		<th><?php __('End Date'); ?></th>
		<th><?php __('Budget'); ?></th>
		<th><?php __('Currency Id'); ?></th>
		<th><?php __('Project Objectives'); ?></th>
		<th><?php __('Issues'); ?></th>
		<th><?php __('Constraint'); ?></th>
		<th><?php __('Remark'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectPhase['Project'] as $project):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $project['id'];?></td>
			<td><?php echo $project['project_name'];?></td>
			<td><?php echo $project['project_manager_id'];?></td>
			<td><?php echo $project['project_phase_id'];?></td>
			<td><?php echo $project['project_priority_id'];?></td>
			<td><?php echo $project['project_status_id'];?></td>
			<td><?php echo $project['start_date'];?></td>
			<td><?php echo $project['planed_end_date'];?></td>
			<td><?php echo $project['end_date'];?></td>
			<td><?php echo $project['budget'];?></td>
			<td><?php echo $project['currency_id'];?></td>
			<td><?php echo $project['project_objectives'];?></td>
			<td><?php echo $project['issues'];?></td>
			<td><?php echo $project['constraint'];?></td>
			<td><?php echo $project['remark'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'projects', 'action' => 'view', $project['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'projects', 'action' => 'edit', $project['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'projects', 'action' => 'delete', $project['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $project['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Project Phase Plans');?></h3>
	<?php if (!empty($projectPhase['ProjectPhasePlan'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th><?php __('Project Planed Phase Id'); ?></th>
		<th><?php __('Project Phase Status Id'); ?></th>
		<th><?php __('Phase Planed Start Date'); ?></th>
		<th><?php __('Phase Planed End Date'); ?></th>
		<th><?php __('Phase Real Start Date'); ?></th>
		<th><?php __('Phase Real End Date'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectPhase['ProjectPhasePlan'] as $projectPhasePlan):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectPhasePlan['id'];?></td>
			<td><?php echo $projectPhasePlan['project_id'];?></td>
			<td><?php echo $projectPhasePlan['project_planed_phase_id'];?></td>
			<td><?php echo $projectPhasePlan['project_phase_status_id'];?></td>
			<td><?php echo $projectPhasePlan['phase_planed_start_date'];?></td>
			<td><?php echo $projectPhasePlan['phase_planed_end_date'];?></td>
			<td><?php echo $projectPhasePlan['phase_real_start_date'];?></td>
			<td><?php echo $projectPhasePlan['phase_real_end_date'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_phase_plans', 'action' => 'view', $projectPhasePlan['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_phase_plans', 'action' => 'edit', $projectPhasePlan['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_phase_plans', 'action' => 'delete', $projectPhasePlan['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectPhasePlan['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Phase Plan', true), array('controller' => 'project_phase_plans', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
