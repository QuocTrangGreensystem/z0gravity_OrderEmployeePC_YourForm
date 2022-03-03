<div class="projectPhasePlans view">
<h2><?php  __('Project Phase Plan');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhasePlan['ProjectPhasePlan']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectPhasePlan['Project']['id'], array('controller' => 'projects', 'action' => 'view', $projectPhasePlan['Project']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Phase'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectPhasePlan['ProjectPhase']['name'], array('controller' => 'project_phases', 'action' => 'view', $projectPhasePlan['ProjectPhase']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Phase Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectPhasePlan['ProjectPhaseStatus']['id'], array('controller' => 'project_phase_statuses', 'action' => 'view', $projectPhasePlan['ProjectPhaseStatus']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phase Planed Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhasePlan['ProjectPhasePlan']['phase_planed_start_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phase Planed End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhasePlan['ProjectPhasePlan']['phase_planed_end_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phase Real Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhasePlan['ProjectPhasePlan']['phase_real_start_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phase Real End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectPhasePlan['ProjectPhasePlan']['phase_real_end_date']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Phase Plan', true), array('action' => 'edit', $projectPhasePlan['ProjectPhasePlan']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Phase Plan', true), array('action' => 'delete', $projectPhasePlan['ProjectPhasePlan']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectPhasePlan['ProjectPhasePlan']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Plans', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Plan', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phases', true), array('controller' => 'project_phases', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase', true), array('controller' => 'project_phases', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Phase Statuses', true), array('controller' => 'project_phase_statuses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Phase Status', true), array('controller' => 'project_phase_statuses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Statuses', true), array('controller' => 'project_statuses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Status', true), array('controller' => 'project_statuses', 'action' => 'add')); ?> </li>
	</ul>
</div>
