<div class="projectAmrSubPrograms form">
<?php echo $this->Form->create('ProjectAmrSubProgram');?>
	<fieldset>
		<legend><?php __('Add Project Amr Sub Program'); ?></legend>
	<?php
		echo $this->Form->input('amr_sub_program');
		echo $this->Form->input('project_amr_program_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Project Amr Sub Programs', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Project Amr Programs', true), array('controller' => 'project_amr_programs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Amr Program', true), array('controller' => 'project_amr_programs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Project Amrs', true), array('controller' => 'project_amrs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project Amr', true), array('controller' => 'project_amrs', 'action' => 'add')); ?> </li>
	</ul>
</div>