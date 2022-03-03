<div class="projectEvolutionTypes view">
<h2><?php  __('Project Evolution Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectEvolutionType['ProjectEvolutionType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Type Evolution'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectEvolutionType['ProjectEvolutionType']['project_type_evolution']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Evolution Type', true), array('action' => 'edit', $projectEvolutionType['ProjectEvolutionType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Evolution Type', true), array('action' => 'delete', $projectEvolutionType['ProjectEvolutionType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectEvolutionType['ProjectEvolutionType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Evolution Types', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution Type', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Evolutions', true), array('controller' => 'project_evolutions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Evolution', true), array('controller' => 'project_evolutions', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Project Evolutions');?></h3>
	<?php if (!empty($projectEvolutionType['ProjectEvolution'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Project Id'); ?></th>
		<th><?php __('Project Evolution'); ?></th>
		<th><?php __('Project Evolution Type Id'); ?></th>
		<th><?php __('Evolution Applicant'); ?></th>
		<th><?php __('Evolution Date Validated'); ?></th>
		<th><?php __('Evolution Validator'); ?></th>
		<th><?php __('Supplementary Budget'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($projectEvolutionType['ProjectEvolution'] as $projectEvolution):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $projectEvolution['id'];?></td>
			<td><?php echo $projectEvolution['project_id'];?></td>
			<td><?php echo $projectEvolution['project_evolution'];?></td>
			<td><?php echo $projectEvolution['project_evolution_type_id'];?></td>
			<td><?php echo $projectEvolution['evolution_applicant'];?></td>
			<td><?php echo $projectEvolution['evolution_date_validated'];?></td>
			<td><?php echo $projectEvolution['evolution_validator'];?></td>
			<td><?php echo $projectEvolution['supplementary_budget'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'project_evolutions', 'action' => 'view', $projectEvolution['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'project_evolutions', 'action' => 'edit', $projectEvolution['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'project_evolutions', 'action' => 'delete', $projectEvolution['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectEvolution['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Project Evolution', true), array('controller' => 'project_evolutions', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
