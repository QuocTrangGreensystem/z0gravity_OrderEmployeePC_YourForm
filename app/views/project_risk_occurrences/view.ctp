<div class="projectRiskOccurrences view">
<h2><?php  __('Project Risk Occurrence');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRiskOccurrence['ProjectRiskOccurrence']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Risk Occurrence'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRiskOccurrence['ProjectRiskOccurrence']['risk_occurrence']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Risk Occurrence', true), array('action' => 'edit', $projectRiskOccurrence['ProjectRiskOccurrence']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Risk Occurrence', true), array('action' => 'delete', $projectRiskOccurrence['ProjectRiskOccurrence']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectRiskOccurrence['ProjectRiskOccurrence']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risk Occurrences', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk Occurrence', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risks', true), array('controller' => 'project_risks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk', true), array('controller' => 'project_risks', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Risks');?></h3>
	<?php if (!empty($projectRiskOccurrence['ProjectRisk'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Risk'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th><?php __('Project Risk Severity Id'); ?></th>
		<th><?php __('Project Risk Occurrence Id'); ?></th>
		<th><?php __('Risk Assign To'); ?></th>
		<th><?php __('Risk Close Date'); ?></th>
		<th><?php __('Actions Manage Risk'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectRiskOccurrence['ProjectRisk'] as $projectRisk):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectRisk['id'];?></td>
			<td><?php echo $projectRisk['project_risk'];?></td>
			<td><?php echo $projectRisk['project_id'];?></td>
			<td><?php echo $projectRisk['project_risk_severity_id'];?></td>
			<td><?php echo $projectRisk['project_risk_occurrence_id'];?></td>
			<td><?php echo $projectRisk['risk_assign_to'];?></td>
			<td><?php echo $projectRisk['risk_close_date'];?></td>
			<td><?php echo $projectRisk['actions_manage_risk'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_risks', 'action' => 'view', $projectRisk['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_risks', 'action' => 'edit', $projectRisk['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_risks', 'action' => 'delete', $projectRisk['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectRisk['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Risk', true), array('controller' => 'project_risks', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
