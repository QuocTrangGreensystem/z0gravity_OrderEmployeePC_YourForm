<div class="projectAmrCostControls view">
<h2><?php  __('Project Amr Cost Control');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectAmrCostControl['ProjectAmrCostControl']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Amr Cost Control'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectAmrCostControl['ProjectAmrCostControl']['amr_cost_control']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Amr Cost Control', true), array('action' => 'edit', $projectAmrCostControl['ProjectAmrCostControl']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Amr Cost Control', true), array('action' => 'delete', $projectAmrCostControl['ProjectAmrCostControl']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectAmrCostControl['ProjectAmrCostControl']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Amr Cost Controls', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Amr Cost Control', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Amrs', true), array('controller' => 'project_amrs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Amr', true), array('controller' => 'project_amrs', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Amrs');?></h3>
	<?php if (!empty($projectAmrCostControl['ProjectAmr'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th><?php __('Project Amr Program Id'); ?></th>
		<th><?php __('Project Amr Sub Program Id'); ?></th>
		<th><?php __('Project Amr Category Id'); ?></th>
		<th><?php __('Project Amr Sub Category Id'); ?></th>
		<th><?php __('Project Manager Id'); ?></th>
		<th><?php __('Budget'); ?></th>
		<th><?php __('Currency Id'); ?></th>
		<th><?php __('Project Amr Status Id'); ?></th>
		<th><?php __('Project Amr Mep Date'); ?></th>
		<th><?php __('Project Amr Progression'); ?></th>
		<th><?php __('Project Phases Id'); ?></th>
		<th><?php __('Project Amr Cost Control Id'); ?></th>
		<th><?php __('Project Amr Organization Id'); ?></th>
		<th><?php __('Project Amr Plan Id'); ?></th>
		<th><?php __('Project Amr Perimeter Id'); ?></th>
		<th><?php __('Project Amr Risk Control Id'); ?></th>
		<th><?php __('Project Amr Problem Control Id'); ?></th>
		<th><?php __('Project Amr Risk Information'); ?></th>
		<th><?php __('Project Amr Problem Information'); ?></th>
		<th><?php __('Project Amr Solution'); ?></th>
		<th><?php __('Project Amr Solution Description'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectAmrCostControl['ProjectAmr'] as $projectAmr):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectAmr['id'];?></td>
			<td><?php echo $projectAmr['project_id'];?></td>
			<td><?php echo $projectAmr['project_amr_program_id'];?></td>
			<td><?php echo $projectAmr['project_amr_sub_program_id'];?></td>
			<td><?php echo $projectAmr['project_amr_category_id'];?></td>
			<td><?php echo $projectAmr['project_amr_sub_category_id'];?></td>
			<td><?php echo $projectAmr['project_manager_id'];?></td>
			<td><?php echo $projectAmr['budget'];?></td>
			<td><?php echo $projectAmr['currency_id'];?></td>
			<td><?php echo $projectAmr['project_amr_status_id'];?></td>
			<td><?php echo $projectAmr['project_amr_mep_date'];?></td>
			<td><?php echo $projectAmr['project_amr_progression'];?></td>
			<td><?php echo $projectAmr['project_phases_id'];?></td>
			<td><?php echo $projectAmr['project_amr_cost_control_id'];?></td>
			<td><?php echo $projectAmr['project_amr_organization_id'];?></td>
			<td><?php echo $projectAmr['project_amr_plan_id'];?></td>
			<td><?php echo $projectAmr['project_amr_perimeter_id'];?></td>
			<td><?php echo $projectAmr['project_amr_risk_control_id'];?></td>
			<td><?php echo $projectAmr['project_amr_problem_control_id'];?></td>
			<td><?php echo $projectAmr['project_amr_risk_information'];?></td>
			<td><?php echo $projectAmr['project_amr_problem_information'];?></td>
			<td><?php echo $projectAmr['project_amr_solution'];?></td>
			<td><?php echo $projectAmr['project_amr_solution_description'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_amrs', 'action' => 'view', $projectAmr['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_amrs', 'action' => 'edit', $projectAmr['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_amrs', 'action' => 'delete', $projectAmr['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectAmr['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Amr', true), array('controller' => 'project_amrs', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
