<div class="projectAmrProblemControls form">
<?php echo $this->Form->create('ProjectAmrProblemControl');?>
	<fieldset>
		<legend><?php __('Edit Project Amr Problem Control'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('amr_problem_control');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectAmrProblemControl.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectAmrProblemControl.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Amr Problem Controls', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Amrs', true), array('controller' => 'project_amrs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Amr', true), array('controller' => 'project_amrs', 'action' => 'add')); ?> </li>
	</ul>
</div>