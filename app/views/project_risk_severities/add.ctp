<div class="projectRiskSeverities form">
<?php echo $this->Form->create('ProjectRiskSeverity');?>
	<fieldset>
		<legend><?php __('Add Project Risk Severity'); ?></legend>
	<?php
		echo $this->Form->input('risk_severity');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Risk Severities', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Risks', true), array('controller' => 'project_risks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk', true), array('controller' => 'project_risks', 'action' => 'add')); ?> </li>
	</ul>
</div>