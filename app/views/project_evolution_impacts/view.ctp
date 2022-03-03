<div class="projectEvolutionImpacts view">
<h2><?php  __('Project Evolution Impact');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectEvolutionImpact['ProjectEvolutionImpact']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Evolution Impact'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectEvolutionImpact['ProjectEvolutionImpact']['evolution_impact']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Evolution Impact', true), array('action' => 'edit', $projectEvolutionImpact['ProjectEvolutionImpact']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Evolution Impact', true), array('action' => 'delete', $projectEvolutionImpact['ProjectEvolutionImpact']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectEvolutionImpact['ProjectEvolutionImpact']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Evolution Impacts', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution Impact', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Evolution Impact Refers', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution Impact Refer', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Evolution Impact Refers');?></h3>
	<?php if (!empty($projectEvolutionImpact['ProjectEvolutionImpactRefer'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Evolution Id'); ?></th>
		<th><?php __('Project Evolution Impact Id'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectEvolutionImpact['ProjectEvolutionImpactRefer'] as $projectEvolutionImpactRefer):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectEvolutionImpactRefer['id'];?></td>
			<td><?php echo $projectEvolutionImpactRefer['project_evolution_id'];?></td>
			<td><?php echo $projectEvolutionImpactRefer['project_evolution_impact_id'];?></td>
			<td><?php echo $projectEvolutionImpactRefer['project_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'view', $projectEvolutionImpactRefer['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'edit', $projectEvolutionImpactRefer['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'delete', $projectEvolutionImpactRefer['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectEvolutionImpactRefer['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Evolution Impact Refer', true), array('controller' => 'project_evolution_impact_refers', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
