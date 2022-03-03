<div class="projectRisks view">
<h2><?php  __('Project Risk');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRisk['ProjectRisk']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Risk'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRisk['ProjectRisk']['project_risk']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectRisk['Project']['id'], array('controller' => 'projects', 'action' => 'view', $projectRisk['Project']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Risk Severity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectRisk['ProjectRiskSeverity']['id'], array('controller' => 'project_risk_severities', 'action' => 'view', $projectRisk['ProjectRiskSeverity']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Project Risk Occurrence'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($projectRisk['ProjectRiskOccurrence']['id'], array('controller' => 'project_risk_occurrences', 'action' => 'view', $projectRisk['ProjectRiskOccurrence']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Risk Assign To'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRisk['ProjectRisk']['risk_assign_to']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Risk Close Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRisk['ProjectRisk']['risk_close_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Actions Manage Risk'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $projectRisk['ProjectRisk']['actions_manage_risk']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Project Risk', true), array('action' => 'edit', $projectRisk['ProjectRisk']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Project Risk', true), array('action' => 'delete', $projectRisk['ProjectRisk']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $projectRisk['ProjectRisk']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risks', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risk Severities', true), array('controller' => 'project_risk_severities', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk Severity', true), array('controller' => 'project_risk_severities', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Risk Occurrences', true), array('controller' => 'project_risk_occurrences', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk Occurrence', true), array('controller' => 'project_risk_occurrences', 'action' => 'add')); ?> </li>
	</ul>
</div>
