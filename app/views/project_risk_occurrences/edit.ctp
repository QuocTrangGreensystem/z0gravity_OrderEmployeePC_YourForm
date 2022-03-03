<div class="projectRiskOccurrences form">
<?php echo $this->Form->create('ProjectRiskOccurrence');?>
	<fieldset>
		<legend><?php __('Edit Project Risk Occurrence'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('risk_occurrence');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ProjectRiskOccurrence.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ProjectRiskOccurrence.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Project Risk Occurrences', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Risks', true), array('controller' => 'project_risks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Risk', true), array('controller' => 'project_risks', 'action' => 'add')); ?> </li>
	</ul>
</div>