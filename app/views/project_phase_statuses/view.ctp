<div class="projectPhaseStatuses view">
<h2><?php  __('Project Phase Status');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhaseStatus['ProjectPhaseStatus']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phase Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhaseStatus['ProjectPhaseStatus']['phase_status']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Phase Status', true), array('action' => 'edit', $projectPhaseStatus['ProjectPhaseStatus']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Phase Status', true), array('action' => 'delete', $projectPhaseStatus['ProjectPhaseStatus']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectPhaseStatus['ProjectPhaseStatus']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Statuses', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Status', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Plans', true), array('controller' => 'project_phase_plans', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Plan', true), array('controller' => 'project_phase_plans', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Phase Plans');?></h3>
	<?php if (!empty($projectPhaseStatus['ProjectPhasePlan'])):?>
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
		foreach ($projectPhaseStatus['ProjectPhasePlan'] as $projectPhasePlan):
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
