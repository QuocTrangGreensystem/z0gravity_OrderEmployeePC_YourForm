<div class="projectTypes form">
<?php echo $this->Form->create('ProjectType');?>
	<fieldset>
		<legend><?php __('Add Project Type'); ?></legend>
	<?php
		echo $this->Form->input('project_type');
		echo $this->Form->input('company_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Companies', true), array('controller' => 'companies', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Company', true), array('controller' => 'companies', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Sub Types', true), array('controller' => 'project_sub_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Sub Type', true), array('controller' => 'project_sub_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects', true), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project', true), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>