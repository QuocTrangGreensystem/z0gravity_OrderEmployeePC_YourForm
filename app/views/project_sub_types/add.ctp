<div class="projectSubTypes form">
<?php echo $this->Form->create('ProjectSubType');?>
	<fieldset>
		<legend><?php __('Add Project Sub Type'); ?></legend>
	<?php
		echo $this->Form->input('project_type_id');
		echo $this->Form->input('project_sub_type');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Sub Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Types', true), array('controller' => 'project_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Type', true), array('controller' => 'project_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>