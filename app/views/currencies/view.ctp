<div class="currencies view">
<h2><?php  __('Currency');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $currency['Currency']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Sign Currency'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $currency['Currency']['sign_currency']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $currency['Currency']['description']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Currency', true), array('action' => 'edit', $currency['Currency']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Currency', true), array('action' => 'delete', $currency['Currency']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $currency['Currency']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Currencies', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Currency', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Projects');?></h3>
	<?php if (!empty($currency['Project'])):?>
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
		foreach ($currency['Project'] as $project):
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
