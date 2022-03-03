<div class="projectAmrRiskControls form">
<?php echo $this->Form->create('ProjectAmrRiskControl');?>
	<fieldset>
		<legend><?php __('Edit Project Amr Risk Control'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('amr_risk_control');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectAmrRiskControl.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectAmrRiskControl.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Amr Risk Controls', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Amrs', true), array('controller' => 'project_amrs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Amr', true), array('controller' => 'project_amrs', 'action' => 'add')); ?> </li>
	</ul>
</div>